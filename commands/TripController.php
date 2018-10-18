<?php

namespace app\commands;

use app\components\ConsoleController;
use app\models\Queue;
use app\models\Trip;
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
            ->andWhere(['<=', 'start_time', time()])
            ->orderBy(['seats' => SORT_DESC, 'created_at' => SORT_DESC])->all();
        if (!empty($trips)) {
            /** @var Trip $trip */
            foreach ($trips as $trip) {
                $schedule = json_decode($trip->schedule);
                if (is_array($schedule) && in_array(intval(date('N')), $schedule)) {
                    $trip->schedule = '';
                    Trip::cloneTrip($trip, Trip::STATUS_CREATED);
                }
            }
            Queue::processingQueue();
        }
    }
}
