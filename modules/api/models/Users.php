<?php namespace app\modules\api\models;

use app\models\Blacklist;
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
        if (isset ($array['phone']) && !empty ($array['phone'])) $array['phone'] = (string)$array['phone'];

        $image_file = UploadFiles::findOne($this->image);
        if ($image_file) {
            $array['image_url'] = $image_file->file;
        } else $array['image_url'] = null;

        if ($this->type == User::TYPE_DRIVER) {

            RestFul::updateDriverAccept();

            /** @var \app\models\RestFul $inAccept */
            $inAccept = RestFul::find()->where([
                'AND',
                ['=', 'type', RestFul::TYPE_DRIVER_ACCEPT],
                ['=', 'user_id', $this->id],
                ['=', 'message', json_encode(['status' => 'request'])],
                ['>', 'created_at', time() - 300],
            ])->one();

            $array['accept'] = !empty($inAccept) ? 1 : 0;

            $inQueue = Line::find()->where(['status' => Line::STATUS_QUEUE, 'driver_id' => $this->id])->one();
            $array['queue'] = !empty($inQueue) ? 1 : 0;

            /** @var \app\models\Line $onLine */
            $onLine = Line::find()->where(['status' => Line::STATUS_IN_PROGRESS, 'driver_id' => $this->id])->one();
            $array['line_id'] = !empty($onLine) ? $onLine->id : 0;
        }

        if ($this->type == User::TYPE_PASSENGER) {

            RestFul::updatePassengerAccept();

            /** @var \app\models\RestFul $inAccept */
            $inAccept = RestFul::find()->where([
                'AND',
                ['=', 'type', RestFul::TYPE_PASSENGER_ACCEPT],
                ['=', 'user_id', $this->id],
                ['=', 'message', json_encode(['status' => 'request'])],
                ['>', 'created_at', time() - 300],
            ])->one();

            $array['accept'] = !empty($inAccept) ? 1 : 0;

            /** @var \app\models\Line $inQueue */
            $inQueue = Trip::find()->where(['status' => Trip::STATUS_CREATED, 'user_id' => $this->id])->one();
            $array['queue'] = !empty($inQueue) ? 1 : 0;

            $array['trip_id'] = !empty($inQueue) ? $inQueue->id : 0;
        }

        $array['rating'] = $this->getRating();

        $blacklist = Blacklist::find()->where(['status' => Blacklist::STATUS_BLACKLISTED, 'user_id' => $this->id])->one();
        $array['blacklisted'] = !empty($blacklist) ? 1 : 0;

        return $array;
    }
}
