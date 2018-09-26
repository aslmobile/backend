<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "watchdog".
 *
 * @property int $id
 * @property int $type
 * @property int $user_id
 * @property string $message
 * @property string $baggage
 * @property string $uip
 * @property int $created_at
 * @property int $updated_at
 */
class RestFul extends \yii\db\ActiveRecord
{
    const
        TYPE_LOG = -1,
        TYPE_DRIVER_ACCEPT = 1,
        TYPE_DRIVER_ACCEPT_DONE = 2,
        TYPE_DRIVER_ACCEPT_CANCELLED = 3,
        TYPE_DRIVER_CHECKPOINT_ARRIVE = 4,
        TYPE_DRIVER_HANDLE_ROUTE = 5,
        TYPE_PASSENGER_ACCEPT = 6,
        TYPE_PASSENGER_DECLINE = 7,
        TYPE_PASSENGER_ACCEPT_SEAT = 7;

    public static function tableName()
    {
        return 'watchdog';
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
            [['type', 'user_id', 'uip'], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['message', 'baggage', 'uip'], 'string'],
            ['type', 'default', 'value' => self::TYPE_LOG]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'type' => Yii::t('app', "Type"),
            'user_id' => Yii::t('app', "User ID"),
            'message' => Yii::t('app', "Message"),
            'baggage' => Yii::t('app', "Baggage"),
            'uip' => Yii::t('app', "User IP"),
            'created_at' => Yii::t('app', "Created"),
            'updated_at' => Yii::t('app', "Updated")
        ];
    }
}
