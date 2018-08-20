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
    const TYPE_USER = 0;
    const TYPE_DRIVER = 3;
    const TYPE_PASSENGER = 4;

    public $type;
    public $action_type;

    public $status;
    public $user_id;
    public $driver_id;
    public $vehicle_id;
    public $route_id;
    public $line_id;
    public $start_point_id;
    public $end_point_id;
    public $transaction_type;
    public $transaction_status;
    public $transaction_amount;
    public $transaction_gateway;

    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    'driver_id',
                    'passenger_id',
                    'vehicle_id',
                    'route_id',
                    'line_id',
                    'start_point_id',
                    'end_point_id',
                    'status',
                    'transaction_type',
                    'transaction_gateway',
                    'action_type'
                ],
                'integer'
            ],
            [['transaction_status'], 'string'],
            [['transaction_amount'], 'number']
        ];
    }

    public function attributeLabels()
    {
        return [
            'status'                => Yii::t('app', "Статус"),
            'user_id'               => Yii::t('app', "Пользователь"),
            'driver_id'             => Yii::t('app', "Водитель"),
            'passenger_id'          => Yii::t('app', "Пассажир"),
            'vehicle_id'            => Yii::t('app', "Автомобиль"),
            'route_id'              => Yii::t('app', "Маршрут"),
            'line_id'               => Yii::t('app', "Линия"),
            'start_point_id'        => Yii::t('app', "Начальная точка"),
            'end_point_id'          => Yii::t('app', "Конечная точка"),
            'transaction_type'      => Yii::t('app', "Тип транзакции"),
            'transaction_status'    => Yii::t('app', "Статус транзакции"),
            'transaction_amount'    => Yii::t('app', "Сумма транзакции"),
            'transaction_gateway'   => Yii::t('app', "Шлюз оплаты"),
        ];
    }
}