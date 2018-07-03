<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "line".
 *
 * @property int $id
 * @property int $status
 * @property int $driver_id
 * @property int $vehicle_id
 * @property float $tariff
 * @property int $route_id
 * @property int $startpoint
 * @property int $endpoint
 * @property int $seats
 * @property int $freeseats
 * @property int $starttime
 * @property int $endtime
 * @property string $cancel_reason
 * @property int $created_at
 * @property int $updated_at
 */
class Line extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'line';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'driver_id',
                    'vehicle_id',
                    'route_id',
                    'startpoint',
                    'endpoint',
                    'tariff'
                ],
                'required'
            ],

            [
                [
                    'status',
                    'driver_id',
                    'vehicle_id',
                    'route_id',
                    'startpoint',
                    'endpoint',
                    'seats',
                    'freeseats',
                    'starttime',
                    'endtime'
                ],
                'integer'
            ],

            [
                [
                    'tariff'
                ],
                'number'
            ],

            [
                [
                    'cancel_reason'
                ],
                'string'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', "ID"),
            'driver_id'         => Yii::t('app', "Водитель"),
            'vehicle_id'        => Yii::t('app', "Автомобиль"),
            'route_id'          => Yii::t('app', "Маршрут"),
            'startpoint'        => Yii::t('app', "Начальная точка"),
            'endpoint'          => Yii::t('app', "Конечная точка"),
            'tariff'            => Yii::t('app', "Тариф"),
            'status'            => Yii::t('app', "Статус"),
            'seats'             => Yii::t('app', "Мест"),
            'freeseats'         => Yii::t('app', "Свободных мест"),
            'starttime'         => Yii::t('app', "Время отправления"),
            'endtime'           => Yii::t('app', "Время прибытия"),
            'cancel_reason'     => Yii::t('app', "Причина отмены"),
            'created_at'        => Yii::t('app', "Создано"),
            'updated_at'        => Yii::t('app', "Обновлено")
        ];
    }
}
