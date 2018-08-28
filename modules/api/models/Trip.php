<?php namespace app\modules\api\models;

use app\models\LuggageType;
use app\models\Notifications;
use app\models\TripLuggage;

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

    public function getUser()
    {
        return Users::findOne($this->user_id);
    }

    public function getBaggage()
    {
        $luggages = TripLuggage::find()->andWhere([
            'AND',
            ['=', 'unique_id', $this->luggage_unique_id]
        ])->all();

        $baggages = [];
        if ($luggages && count($luggages) > 0) foreach ($luggages as $luggage)
        {
            /** @var \app\models\TripLuggage $luggage */
            $baggage = LuggageType::findOne($luggage->luggage_type);
            $baggages[] = [
                'id' => $baggage->id,
                'title' => $baggage->title,
                'need_place' => $baggage->need_place
            ];
        }

        return $baggages;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->status != $this->oldStatus && $this->status == self::STATUS_WAY)
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
        else $array['startpoint'] = null;

        if ($this->endpoint) $array['endpoint'] = $this->endpoint->toArray();
        else $array['endpoint'] = null;

        if ($this->route) $array['route'] = $this->route->toArray();
        else $array['route'] = null;

        if ($this->vehicle) $array['vehicle'] = $this->vehicle->toArray();
        else $array['vehicle'] = null;

        if ($this->driver) $array['driver'] = $this->driver->toArray();
        else $array['driver'] = null;

        if ($this->user) $array['passenger'] = $this->user->toArray();
        else $array['passenger'] = null;

        if ($this->baggage) $array['baggage'] = $this->baggage;
        else $array['baggage'] = [];

        return $array;
    }
}