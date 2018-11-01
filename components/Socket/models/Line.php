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

        $driver = Users::findOne($this->driver_id);
        $vehicle = Vehicles::findOne($this->vehicle_id);
        $route = Route::findOne($this->route_id);
        $startpoint = Checkpoint::findOne($this->startpoint);
        $endpoint = Checkpoint::findOne($this->endpoint);


        foreach ($array as $field => $value) {
            if ($field == 'driver_id') $array[$field] = !empty($driver) ? $driver->toArray() : (object)['id' => -1];
            if ($field == 'vehicle_id') $array[$field] = !empty($vehicle) ? $vehicle->toArray() : (object)['id' => -1];
            if ($field == 'route_id') $array[$field] = !empty($route) ? $route->toArray() : (object)['id' => -1];
            if ($field == 'startpoint') $array[$field] = !empty($startpoint) ? $startpoint->toArray() : (object)['id' => -1];
            if ($field == 'endpoint') $array[$field] = !empty($endpoint) ? $endpoint->toArray() : (object)['id' => -1];
        }

        return $array;
    }
}
