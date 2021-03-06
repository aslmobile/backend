<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vehicle_type".
 *
 * @property int $id
 * @property string $title
 * @property string $image
 * @property integer $status
 * @property integer $max_seats
 * @property int $created_at
 * @property int $updated_at
 */
class VehicleType extends \yii\db\ActiveRecord
{
    const
        STATUS_ACTIVE = 1,
        STATUS_DISABLED = 0;

    public static function tableName()
    {
        return 'vehicle_type';
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
            [['title', 'image'], 'required'],
            ['image', 'string'],
            [['max_seats', 'status'], 'integer'],
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
            'image' => Yii::t('app', "Изображение"),
            'status' => Yii::t('app', "Статус"),
            'max_seats' => Yii::t('app', "Мест"),
            'created_at' => Yii::t('app', "Создано"),
            'updated_at' => Yii::t('app', "Обновлено")
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        if (empty($this->image)) $this->image = '/files/sedan.png';
    }
}
