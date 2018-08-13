<?php namespace app\modules\api\models;

use app\models\Notifications;

class Trip extends \app\models\Trip
{
    const
        STATUS_PENDING = 1,
        STATUS_TRIP = 2,
        STATUS_ENDED = 3,
        STATUS_CANCELED = 4;

    const
        PAYMENT_STATUS_WAITING = 1,
        PAYMENT_STATUS_REJECTED = 2,
        PAYMENT_STATUS_CANCELLED = 3,
        PAYMENT_STATUS_PAID = 4;

    const
        PAYMENT_TYPE_CARD = 1,
        PAYMENT_TYPE_KM = 2,
        PAYMENT_TYPE_CASH = 3;

    protected $oldStatus;

    public function getUser()
    {
        return Users::findOne($this->user_id);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->status != $this->oldStatus && $this->status == self::STATUS_TRIP)
        {
            // TODO: Отправка подтверждения о выезде
            Notifications::create(Notifications::NTP_TRIP_READY, $this->user_id, true, \Yii::t('app', "Ваша машина готова к выезду."));
        }
    }

    public function beforeSave($insert)
    {
        $this->oldStatus = $this->getOldAttribute('status');

        return parent::beforeSave($insert);
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        if ($this->startpoint) $array['startpoint'] = $this->startpoint->toArray();
        if ($this->endpoint) $array['endpoint'] = $this->endpoint->toArray();
        if ($this->route) $array['route'] = $this->route->toArray();
        if ($this->vehicle) $array['vehicle'] = $this->vehicle->toArray();
        if ($this->driver) $array['driver'] = $this->driver->toArray();

        return $array;
    }
}