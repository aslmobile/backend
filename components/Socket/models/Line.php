<?php namespace app\components\Socket\models;

use app\models\Checkpoint;
use app\models\Route;
use app\modules\api\models\Users;
use app\modules\api\models\Vehicles;

class Line extends \app\models\Line
{
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        foreach ($array as $field => $value)
        {
            if ($field == 'driver_id') $array[$field] = Users::findOne($this->driver_id)->toArray();
            if ($field == 'vehicle_id') $array[$field] = Vehicles::findOne($this->vehicle_id)->toArray();
            if ($field == 'route_id') $array[$field] = Route::findOne($this->route_id)->toArray();
            if ($field == 'startpoint') $array[$field] = Checkpoint::findOne($this->startpoint)->toArray();
            if ($field == 'endpoint') $array[$field] = Checkpoint::findOne($this->endpoint)->toArray();
        }

        return $array;
    }
}