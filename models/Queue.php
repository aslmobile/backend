<?php


namespace app\models;


use app\components\ArrayQuery\ArrayQuery;
use app\components\Socket\SocketPusher;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Queue extends Model
{

    /**
     * Collecting passengers trips and sort them between drivers lines
     */
    public static function processingQueue()
    {

        $lines = \app\modules\api\models\Line::find()
            ->where(['status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING, Line::STATUS_IN_PROGRESS]])
            ->andWhere(['>', 'freeseats', 0])
            ->orderBy(['freeseats' => SORT_ASC, 'created_at' => SORT_ASC])
            ->all();

        $trips = \app\modules\api\models\Trip::find()
            ->where(['status' => Trip::STATUS_CREATED])
            //->andWhere(['<=', 'start_time', time()])
            ->orderBy(['seats' => SORT_DESC, 'created_at' => SORT_ASC])
            ->all();

        $query = new ArrayQuery();
        $query->from(ArrayHelper::index($trips, 'id'));

        /** @var  $line Line */
        foreach ($lines as $key => $line) {
            $applicants = self::getQueue($line, $query);
            if ($line->ready) {
                $notifications = Notifications::create(Notifications::NTD_TRIP_FIRST, [$line->driver_id]);
                foreach ($notifications as $notification) Notifications::send($notification);
                $ids = ArrayHelper::getColumn($applicants, 'id');
                $applicants = ArrayHelper::getColumn($applicants, 'user_id');
                self::unsetQueue($ids, $query);
                if (!empty($ids)) self::send($applicants, $line);
            }

        }

        $socket = new SocketPusher(['authkey' => \Yii::$app->params['socket']['authkey_server']]);
        $socket->push(base64_encode(json_encode(['action' => "changeQueue", 'data' => ['message_id' => time()]])));

    }

    /**
     * @param $ids
     * @param $query ArrayQuery
     */
    public static function unsetQueue($ids, &$query)
    {
        foreach ($ids as $id) if (isset($query->from[$id])) unset($query->from[$id]);
    }

    /**
     * @param $line Line
     * @param $trips ArrayQuery
     * @return array
     */
    public static function getQueue(&$line, $trips)
    {

        $data = $trips->where(['route_id' => $line->route_id])
            ->andWhere(['OR', ['vehicle_type_id' => $line->vehicle_type_id], ['vehicle_type_id' => 0]])
            ->orderBy(['seats' => SORT_DESC, 'created_at' => SORT_DESC])->all();

        $queue = [];
        $need = 0;

        /** @var $trip Trip[] */
        foreach (ArrayHelper::index($data, 'id') as $key => $trip) {
            $need += $trip['seats'];
            if ($line->freeseats < $need) {
                continue;
            } elseif ($line->freeseats > $need) {
                $queue = $queue + [$key => $trip];
            } elseif ($line->freeseats == $need) {
                $queue = $queue + [$key => $trip];
                $line->ready = true;
                break;
            }
        }

        return $queue;
    }

    /**
     * @param $applicants integer[]
     * @param $line Line
     * @return bool
     */
    public static function send($applicants, $line)
    {

        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $line->driver_id]);
        if (!$device) return false;
        $socket = new SocketPusher(['authkey' => $device->auth_token]);

        foreach ($applicants as $user_id) {
            $socket->push(base64_encode(json_encode([
                'action' => "readyPassengerTrip",
                'notifications' => Notifications::create(
                    Notifications::NTP_TRIP_READY,
                    [$user_id],
                    '',
                    $line->driver_id
                ),
                'data' => ['message_id' => time(), 'addressed' => [$user_id], 'line' => $line->toArray(), 'passenger' => $user_id]
            ])));
        }

        return true;
    }

}
