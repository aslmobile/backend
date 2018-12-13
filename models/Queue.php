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
            ->where(['status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]])
            ->andWhere(['>', 'freeseats', 0])
            ->orderBy(['freeseats' => SORT_ASC, 'created_at' => SORT_ASC])
            ->all();

        $trips = \app\modules\api\models\Trip::find()
            ->where(['status' => Trip::STATUS_CREATED])
            ->andWhere(['<=', 'queue_time', time()])
            ->andWhere(['line_id' => 0])
            ->orderBy(['seats' => SORT_DESC, 'created_at' => SORT_ASC])
            ->all();

        $query = new ArrayQuery();
        $query->from(ArrayHelper::index($trips, 'id'));

        /** @var  $line Line */
        foreach ($lines as $key => $line) {

            $applicants = self::getQueue($line, $query);
            $ids = ArrayHelper::getColumn($applicants, 'id');

            //self::unsetQueue($ids, $query);
            //if (!empty($ids)) self::send($applicants, $ids, $line);

            if ($line->ready) {
                self::unsetQueue($ids, $query);
                self::send($applicants, $ids, $line);
                $notifications = Notifications::create(Notifications::NTD_TRIP_FIRST, [$line->driver_id]);
                foreach ($notifications as $notification) Notifications::send($notification);
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
            ->andWhere(['CALLBACK', function ($data) use ($line) {
                return !empty($data['not'])?(!in_array($line->id, json_decode($data['not']))):true;
            }])
            ->andWhere(['OR', ['vehicle_type_id' => $line->vehicle_type_id], ['vehicle_type_id' => 0]])
            ->orderBy(['seats' => SORT_DESC, 'created_at' => SORT_ASC])->all();

        $queue = [];
        $data = ArrayHelper::index($data, 'id');
        $free_seats = $line->freeseats;

        /** @var $trip Trip[] */
        foreach ($data as $key => $trip) {
            $seats = $trip['seats'];
            if ($free_seats < $seats) {
                continue;
            } elseif ($free_seats > $seats) {
                $queue[$key] = $trip;
                $free_seats -= $seats;
            } elseif ($free_seats == $seats) {
                $queue[$key] = $trip;
                $line->ready = true;
                break;
            }
        }

        return $queue;
    }

    /**
     * @param $applicants
     * @param $ids
     * @param $line Line
     * @return bool
     */
    public static function send($applicants, $ids, $line)
    {

        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $line->driver_id]);
        if (!$device) return false;
        $socket = new SocketPusher(['authkey' => $device->auth_token]);

        Trip::updateAll(['line_id' => $line->id, 'waiting_time' => time()], ['id' => $ids]);
        Line::updateAll(['freeseats' => 0], ['id' => $line->id]);

        $line->freeseats = 0;

        foreach ($applicants as $applicant) {

            $socket->push(base64_encode(json_encode([
                'action' => "readyPassengerTrip",
                'notifications' => Notifications::create(
                    Notifications::NTP_TRIP_READY,
                    [$applicant['user_id']],
                    '',
                    $line->driver_id
                ),
                'data' => [
                    'message_id' => time(),
                    'addressed' => [$applicant['user_id']],
                    'line' => $line->toArray(),
                    'passenger' => $applicant['user_id'],
                    'timer' => true
                ]
            ])));

        }

        return true;
    }

}
