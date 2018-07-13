<?php namespace app\modules\api\models;

class Trip extends \app\models\Trip
{
    const
        STATUS_PENDING = 1,
        STATUS_TRIP = 2,
        STATUS_ENDED = 3,
        STATUS_CANCELED = 4;

    public function getUser()
    {
        return Users::findOne($this->user_id);
    }
}