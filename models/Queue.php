<?php


namespace app\models;


use app\components\Socket\SocketPusher;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Queue extends Model
{

    public static function processingQueue()
    {
        $lines = Line::find()
            ->where(['status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]])
            ->orderBy(['freeseats' => SORT_ASC, 'created_at' => SORT_DESC])
            ->all();

        $not = [];

        /** @var  $line Line */
        foreach ($lines as $key => $line) {
            $applicants = self::getQueue($line, $not);
            if ($line->ready) {

                $notifications = Notifications::create(Notifications::NTD_TRIP_FIRST, [$line->driver_id]);
                foreach ($notifications as $notification) Notifications::send($notification);

                $ids = ArrayHelper::getColumn($applicants, 'id');
                $not = $not + $ids;
                if(!empty($ids)) self::send($ids, $line);

            }
        }
    }

    /**
     * @param $line Line
     * @param $not integer[]
     * @return array
     */
    public static function getQueue(&$line, $not = [])
    {

        $data = Trip::find()->where([
            'route_id' => $line->route_id,
            'vehicle_type_id' => $line->vehicle_type_id,
            'status' => Trip::STATUS_CREATED
        ]);
        if (!empty($not)) $data->andWhere(['NOT', ['id' => $not]]);
        $data->orderBy(['seats' => SORT_DESC, 'created_at' => SORT_DESC])->all();

        $queue = [];
        $need = 0;

        /** @var $trip Trip */
        foreach ($data as $key => $trip) {
            $need += $trip->seats;
            if ($line->freeseats < $need) {
                continue;
            } elseif ($line->freeseats > $need) {
                $queue[] = $trip;
            } elseif ($line->freeseats == $need) {
                $queue[] = $trip;
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

        $socket->push(base64_encode(json_encode([
            'action' => "readyPassengerTrip",
            'notifications' => Notifications::create(Notifications::NTP_TRIP_READY, $applicants, '', $line->driver_id),
            'data' => ['message_id' => time(), 'addressed' => $applicants, 'line' => $line]
        ])));

        return true;
    }

}
