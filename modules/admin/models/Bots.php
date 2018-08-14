<?php namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

class Bots extends Model
{
    const TYPE_DRIVER = 3;
    const TYPE_PASSENGER = 4;

    public $type;

    public $driver_id;
    public $vehicle_id;
    public $route_id;

    public function rules()
    {
        return [
            [['driver_id', 'passenger_id', 'vehicle_id', 'route_id', 'start_point_id', 'end_point_id'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'driver_id'     => Yii::t('app', "Водитель"),
            'passenger_id'  => Yii::t('app', "Пассажир"),
            'vehicle_id'    => Yii::t('app', "Автомобиль")
        ];
    }
}