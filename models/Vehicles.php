<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_vehicle".
 *
 * @property int $id
 * @property integer $user_id
 * @property integer $main
 * @property integer $status
 * @property integer $seats
 * @property string $license_plate
 * @property integer $image
 * @property integer $insurance
 * @property integer $insurance2
 * @property integer $license
 * @property integer $license2
 * @property integer $weight
 * @property integer $vehicle_type_id
 * @property integer $vehicle_model_id
 * @property integer $vehicle_brand_id
 * @property float $rating
 * @property int $created_at
 * @property int $updated_at
 */
class Vehicles extends \yii\db\ActiveRecord
{
    const
        STATUS_ADDED = 0,
        STATUS_APPROVED = 1,
        STATUS_WAITING = 2;

    public static function tableName()
    {
        return 'user_vehicle';
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
            [['user_id', 'vehicle_type_id', 'vehicle_model_id', 'vehicle_brand_id'], 'required'],
            [['main', 'weight', 'seats', 'vehicle_type_id', 'vehicle_model_id', 'vehicle_brand_id'], 'integer'],
            [['image', 'insurance', 'insurance2', 'license', 'license2'], 'integer'],
            [['license_plate'], 'string'],
            [['seats'], 'integer', 'min' => 1],
            [['main'], 'integer', 'min' => 0, 'max' => 1],
            [['weight'], 'integer', 'min' => 0],
            [['main'], 'filter', function () {
                // TODO: Check main for current user vehicle
            }],
            ['status', 'default', 'value' => self::STATUS_ADDED]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', "ID"),
            'user_id'           => Yii::t('app', "User"),
            'status'            => Yii::t('app', "Status"),
            'seats'             => Yii::t('app', "Seats"),
            'license_plate'     => Yii::t('app', "License Plate"),
            'image'             => Yii::t('app', "Image"),
            'insurance'         => Yii::t('app', "Insurance Front"),
            'insurance2'        => Yii::t('app', "Insurance Back"),
            'license'           => Yii::t('app', "License Front"),
            'license2'          => Yii::t('app', "License Back"),
            'vehicle_type_id'   => Yii::t('app', "Vehicle Type"),
            'vehicle_brand_id'  => Yii::t('app', "Vehicle Brand"),
            'vehicle_model_id'  => Yii::t('app', "Vehicle Model"),
            'created_at'        => Yii::t('app', "Created"),
            'updated_at'        => Yii::t('app', "Updated")
        ];
    }
}
