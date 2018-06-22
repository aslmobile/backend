<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vehicle_type".
 *
 * @property int $id
 * @property string $title
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
            [['title', 'max_seats'], 'required'],
            [['max_seats', 'status'], 'integer'],
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
            'created_at'        => Yii::t('app', "Created"),
            'updated_at'        => Yii::t('app', "Updated")
        ];
    }
}
