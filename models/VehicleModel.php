<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vehicle_model".
 *
 * @property int $id
 * @property string $title
 * @property integer $status
 * @property integer $max_seats
 * @property string $image
 * @property integer $vehicle_type_id
 * @property integer $vehicle_brand_id
 * @property int $created_at
 * @property int $updated_at
 */
class VehicleModel extends \yii\db\ActiveRecord
{
    const
        STATUS_ACTIVE = 1,
        STATUS_DISABLED = 0;

    public static function tableName()
    {
        return 'vehicle_model';
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
            [['title', 'max_seats', 'vehicle_type_id', 'vehicle_brand_id'], 'required'],
            [['max_seats', 'status', 'vehicle_type_id', 'vehicle_brand_id'], 'integer'],
            [['image'], 'string'],
            [['max_seats'], 'integer', 'min' => 1],
            ['status', 'default', 'value' => self::STATUS_ACTIVE]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', "ID"),
            'title'             => Yii::t('app', "Title"),
            'status'            => Yii::t('app', "Status"),
            'max_seats'         => Yii::t('app', "Max Seats"),
            'vehicle_type_id'   => Yii::t('app', "Vehicle Type"),
            'vehicle_brand_id'  => Yii::t('app', "Vehicle Brand"),
            'image'             => Yii::t('app', "Image"),
            'created_at'        => Yii::t('app', "Created"),
            'updated_at'        => Yii::t('app', "Updated")
        ];
    }
}
