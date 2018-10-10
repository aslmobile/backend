<?php namespace app\modules\api\models;

class Trip extends \app\models\Trip
{
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
    {
        $this->oldStatus = $this->getOldAttribute('status');

        return parent::beforeSave($insert);
    }
}
