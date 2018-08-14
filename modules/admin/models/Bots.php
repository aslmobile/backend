<?php namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * Class Bots
 * @package app\modules\admin\models
 *
 * @property integer $driver_id
 * @property integer $passenger_id
 * @property integer $vehicle_id
 * @property integer $route_id
 * @property integer $start_point_id
 * @property integer $end_point_id
 * @property integer $status
 */
class Bots extends Model
{
    const TYPE_DRIVER = 3;
    const TYPE_PASSENGER = 4;

    public $type;

    public $status;
    public $driver_id;
    public $vehicle_id;
    public $route_id;
    public $start_point_id;
    public $end_point_id;

    public function rules()
    {
        return [
            [['driver_id', 'passenger_id', 'vehicle_id', 'route_id', 'start_point_id', 'end_point_id', 'status'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'status'            => Yii::t('app', "Статус"),
            'driver_id'         => Yii::t('app', "Водитель"),
            'passenger_id'      => Yii::t('app', "Пассажир"),
            'vehicle_id'        => Yii::t('app', "Автомобиль"),
            'route_id'          => Yii::t('app', "Маршрут"),
            'start_point_id'    => Yii::t('app', "Начальная точка"),
            'end_point_id'      => Yii::t('app', "Конечная точка"),
        ];
    }
}