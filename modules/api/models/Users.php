<?php namespace app\modules\api\models;

use app\models\Blacklist;
use app\models\Dispatch;
use app\modules\user\models\User;

class Users extends \app\models\User
{
    public function getScheme()
    {
        $schema = <<<'JSON'
{
    "type": "object",
    "properties": {
        "id": {"type": "integer"},
        "first_name": {"type": "string"},
        "second_name": { "type": "string"},
        "created_at": {"type": "integer"},
        "created_by": {"type": "integer"},
        "updated_at": {"type": "integer"},
        "updated_by": {"type": "integer"},
        "approval_at": {"type": "integer"},
        "approval_by": {"type": "integer"},
        "blocked_at": {"type": "integer"},
        "blocked_by": {"type": "integer"},
        "country_id": {"type": "integer"},
        "city_id": {"type": "integer"},
        "gender": {"type": "integer"},
        "status": {"type": "integer"},
        "type": {"type": "integer"},
        "phone": {"type": "string", "format": "phone"}
    }
}
JSON;

        return $schema;
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        $blacklist = Blacklist::find()->where(['status' => Blacklist::STATUS_BLACKLISTED, 'user_id' => $this->id])->one();
        $image_file = UploadFiles::findOne(['id' => $this->image]);

        if (isset ($array['phone']) && !empty ($array['phone'])) $array['phone'] = (string)$array['phone'];
        if (!empty($image_file)){
            $array['image_url'] = $image_file->file;
        } else {
            $array['image_url'] = null;
        }
        $array['rating'] = $this->getRating();
        $array['blacklisted'] = !empty($blacklist) ? 1 : 0;

        switch ($this->type) {
            case User::TYPE_DRIVER:

                RestFul::updateDriverAccept();

                /** @var \app\models\RestFul $inAccept */
                $inAccept = RestFul::find()->where([
                    'AND',
                    ['=', 'type', RestFul::TYPE_DRIVER_ACCEPT],
                    ['=', 'user_id', $this->id],
                    ['=', 'message', json_encode(['status' => 'request'])],
                    ['>', 'created_at', time() - 300],
                ])->one();

                /** @var \app\models\Line $line */
                $line = Line::find()->where(['status' => [Line::STATUS_QUEUE, Line::STATUS_IN_PROGRESS], 'driver_id' => $this->id])
                    ->orderBy(['created_at' => SORT_DESC])->one();

                $array['accept'] = !empty($inAccept) ? 1 : 0;

                $array['queue'] = (!empty($line) && $line->status == Line::STATUS_QUEUE) ? 1 : 0;
                $array['line_id'] = (!empty($line) && $line->status == Line::STATUS_IN_PROGRESS) ? $line->id : 0;

                break;
            case User::TYPE_PASSENGER:

                $penalty = Trip::findOne(['user_id' => $this->id, 'penalty' => 1]);
                $dispatch = Dispatch::findOne(1);
                if (!$dispatch) $dispatch = (object)['phone' => 70123456789];
                if (!$penalty) $penalty = (object)['id' => -1, 'amount' => 0]; else {
                    $penalty = (object)[
                        'id' => $penalty->id,
                        'amount' => $penalty->amount / 2,
                        'cancel_reason' => $penalty->driver_description,
                        'phone' => $dispatch->phone,
                    ];
                }

                RestFul::updatePassengerAccept();
                RestFul::updatePassengerAcceptSeat();

                /** @var \app\models\RestFul $inAccept */
                $inAccept = RestFul::find()->where([
                    'AND',
                    ['=', 'type', RestFul::TYPE_PASSENGER_ACCEPT],
                    ['=', 'user_id', $this->id],
                    ['=', 'message', json_encode(['status' => 'request'])],
                    ['>', 'created_at', time() - 300],
                ])->one();

                /** @var \app\models\RestFul $inAcceptSeat */
                $inAcceptSeat = RestFul::find()->where([
                    'AND',
                    ['=', 'type', RestFul::TYPE_PASSENGER_ACCEPT_SEAT],
                    ['=', 'user_id', $this->id],
                    ['=', 'message', json_encode(['status' => 'request'])],
                    ['>', 'created_at', time() - 300],
                ])->one();

                /** @var \app\models\Trip $trip */
                $trip = Trip::find()->where(['user_id' => $this->id])
                    ->andWhere(['status' => [Trip::STATUS_CREATED, Trip::STATUS_WAITING, Trip::STATUS_WAY, Trip::STATUS_FINISHED]])
                    ->orderBy(['created_at' => SORT_DESC])->one();

                $array['penalty'] = $penalty;
                $array['accept'] = !empty($inAccept) ? 1 : 0;
                $array['acceptSeat'] = !empty($inAcceptSeat) ? 1 : 0;
                $array['init'] = (!empty($trip) && $trip->status == Trip::STATUS_CREATED) ? 1 : 0;
                $array['queue'] = (!empty($trip) && $trip->status == Trip::STATUS_CREATED && $trip->queue_time <= time()) ? 1 : 0;
                $array['online'] = (!empty($trip) && in_array($trip->status, [Trip::STATUS_WAITING, Trip::STATUS_WAY])) ? 1 : 0;
                $array['trip_id'] = !empty($trip) ? $trip->id : 0;

                break;
        }

        return $array;
    }
}
