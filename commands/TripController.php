<?php

namespace app\commands;

use app\components\ConsoleController;
use app\components\Socket\SocketPusher;
use app\models\Devices;
use app\models\Notifications;
use app\models\Queue;
use app\models\Trip;
use app\models\User;
use app\modules\main\models\Settings;
use Yii;
use yii\base\Module;

class TripController extends ConsoleController
{
    public $coreSettings;

    public function __construct($id, Module $module, array $config = [])
    {
        Yii::setAlias('@webroot', __DIR__ . '../web');
        $this->coreSettings = self::getCoreSettings();

        parent::__construct($id, $module, $config);
    }

    public static function getCoreSettings()
    {
        return Settings::find()->where('id = 1')->one();
    }

    public function actionIndex()
    {
        $trips = Trip::find()->where(['status' => Trip::STATUS_SCHEDULED])
            ->andWhere(['!=', 'queue_time', 0])
            ->andWhere(['<=', 'queue_time', time()])
            ->orderBy(['seats' => SORT_DESC, 'created_at' => SORT_DESC])->all();
        if (!empty($trips)) {

            $socket = new SocketPusher();

            /** @var Trip $trip */
            foreach ($trips as $trip) {
                $schedule = json_decode($trip->schedule);
                if (is_array($schedule) && in_array(intval(date('N')), $schedule)) {
                    $penalty = Trip::findOne(['user_id' => $trip->user_id, 'penalty' => 1]);
                    if (!empty($penalty)) continue;
                    $user = User::findOne($trip->user_id);
                    if (empty($user)) continue;
                    $state = $user->toArray();
                    $trip->queue_time += 60 * 60 * 24;
                    $trip->update(false);
                    if ($state['queue'] || $state['online']) continue;
                    Trip::cloneTrip($trip, Trip::STATUS_CREATED, true);
                    /** @var \app\models\Devices $device */
                    $device = Devices::findOne(['user_id' => $user->id]);
                    if (!empty($device)) {
                        $socket->authkey = $device->auth_token;
                        $socket->push(base64_encode(json_encode([
                            'action' => "startScheduledTrip",
                            'notifications' => [],
                            'data' => ['message_id' => time(), 'addressed' => [$user->id], 'trip' => $trip->toArray()]
                        ])));
                    }
                    $notifications = Notifications::create(Notifications::NTP_TRIP_SCHEDULED_START, [$user->id]);
                    if (is_array($notifications)) foreach ($notifications as $notification) Notifications::send($notification);
                }
            }
            Queue::processingQueue();
        }
    }
}
