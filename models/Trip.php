<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "trip".
 *
 * @property int $id
 * @property int $status
 * @property integer $user_id
 * @property integer $driver_id
 * @property float $amount
 * @property float $tariff
 * @property integer $cancel_reason
 * @property string $passenger_description
 * @property string $driver_description
 * @property string $currency
 * @property int $payment_type
 * @property int $payment_status
 * @property float $passenger_rating
 * @property float $driver_rating
 * @property integer $startpoint_id
 * @property integer $endpoint_id
 * @property integer $route_id
 * @property int $seats
 * @property string $driver_comment
 * @property string $passenger_comment
 * @property integer $vehicle_id
 * @property integer $vehicle_type_id
 * @property string $luggage_unique_id
 * @property integer $line_id
 * @property integer $need_taxi
 * @property integer $taxi_status
 * @property integer $taxi_cancel_reason
 * @property string $taxi_address
 * @property integer $taxi_time
 * @property integer $scheduled
 * @property integer $schedule_id
 * @property integer $start_time
 * @property integer $finish_time
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 */
class Trip extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'trip';
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
                    'user_id',
                    'driver_id',
                    'vehicle_id',
                    'line_id'
                ],
                'required'
            ],

            [
                [
                    'status',
                    'user_id',
                    'driver_id',
                    'cancel_reason',
                    'payment_type',
                    'payment_status',
                    'startpoint_id',
                    'endpoint_id',
                    'route_id',
                    'seats',
                    'vehicle_id',
                    'vehicle_type_id',
                    'line_id',
                    'need_taxi',
                    'taxi_status',
                    'taxi_cancel_reason',
                    'taxi_time',
                    'scheduled',
                    'schedule_id',
                    'start_time',
                    'finish_time'
                ],
                'integer'
            ],

            [
                [
                    'amount',
                    'tariff',
                    'passenger_rating',
                    'driver_rating',
                    'passenger_rating',
                    'driver_rating'
                ],
                'number'
            ],

            [
                [
                    'passenger_description',
                    'driver_description',
                    'currency',
                    'driver_comment',
                    'passenger_comment',
                    'luggage_unique_id',
                    'taxi_address'
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
            'created_at'        => Yii::t('app', "Created"),
            'updated_at'        => Yii::t('app', "Updated")
        ];
    }
}
