<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vehicle_brand".
 *
 * @property int $id
 * @property string $title
 * @property integer $status
 * @property integer $max_seats
 * @property string $image
 * @property integer $vehicle_type_id
 * @property int $created_at
 * @property int $updated_at
 */
class VehicleBrand extends \yii\db\ActiveRecord
{
    const
        STATUS_ACTIVE = 1,
        STATUS_DISABLED = 0;

    public static function tableName()
    {
        return 'vehicle_brand';
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
            [['title'], 'required'],
            [['max_seats', 'status', 'vehicle_type_id'], 'integer'],
            [['image'], 'string'],
//            [['max_seats'], 'integer', 'min' => 1],
            ['status', 'default', 'value' => self::STATUS_ACTIVE]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'title' => Yii::t('app', "Название"),
            'status' => Yii::t('app', "Статус"),
            'max_seats' => Yii::t('app', "Мест"),
            'image' => Yii::t('app', "Фото"),
            'vehicle_type_id' => Yii::t('app', "Тип"),
            'created_at' => Yii::t('app', "Создано"),
            'updated_at' => Yii::t('app', "Обновлено")
        ];
    }
}
