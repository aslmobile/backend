<?php namespace app\modules\api\models;

class Line extends \app\models\Line
{

//    public function afterSave($insert, $changedAttributes)
//    {
//        parent::afterSave($insert, $changedAttributes);
//
//        $stacks = $this->getStacks($this->freeseats);
//        if ($stacks) $this->startTrip($stacks);
//    }

//    public function startTrip($stacks)
//    {
//        /** @var \app\modules\api\models\Trip $trip */
//        foreach ($stacks as $trip)
//        {
//            $vehicle = Vehicles::findOne($this->vehicle_id);
//
//            $trip->status = $trip::STATUS_TRIP;
//            $trip->driver_id = $this->driver_id;
//            $trip->vehicle_id = $vehicle->id;
//            $trip->vehicle_type_id = $vehicle->type;
//
//            $trip->save();
//        }
//    }
//
//    public function getStacks(int $seats)
//    {
//        $passengers = Trip::find()->where(['status' => \app\models\Trip::STATUS_WAITING])->all();
//        if (!$passengers) return false;
//
//        $stack = [];
//
//        /** @var \app\modules\api\models\Trip $passenger */
//        if ($passengers && count($passengers) > 0) foreach ($passengers as $passenger)
//        {
//            if ($seats == 0) break;
//
//            if ($passenger->seats <= $seats)
//            {
//                $stack[] = $passenger;
//                $seats -= $passenger->seats;
//            }
//        }
//
//        if ($seats == 0) return $stack;
//
//        return false;
//    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        if ($this->driver) $array['driver'] = $this->driver->toArray();
        if ($this->vehicle) $array['vehicle'] = $this->vehicle->toArray();
        if ($this->route) $array['route'] = $this->route->toArray();
        if ($this->startPoint) $array['startpoint'] = $this->startPoint->toArray();
        if ($this->endPoint) $array['endpoint'] = $this->endPoint->toArray();

        return $array;
    }
}
