<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "device_session".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $user_type
 * @property string $push_id
 * @property string $device_id
 * @property float $sms_code
 * @property integer $type
 * @property string $auth_token
 * @property string $lang
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $uip
 * @property integer $notifications
 */
class Devices extends ActiveRecord
{
    const
        TYPE_IOS = 1,
        TYPE_ANDROID = 2;

    const
        USER_TYPE_ADMIN = 1,
        USER_TYPE_MANAGER = 2,
        USER_TYPE_DRIVER = 3,
        USER_TYPE_PASSENGER = 4;

    const
        STATUS_PENDING = 0,
        STATUS_APPROVED = 1,
        STATUS_BLOCKED = 9;

    const
        NOTIFICATION_ENABLED = 1,
        NOTIFICATION_DISABLED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'device_session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'device_id'], 'required'],
            [['user_id', 'type', 'user_type', 'notifications'], 'integer'],
            [['sms_code'], 'number'],
            [['push_id', 'device_id', 'auth_token', 'lang', 'uip'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'user_id' => Yii::t('app', "User ID"),
            'user_type' => Yii::t('app', "User Type"),
            'push_id' => Yii::t('app', "Push ID"),
            'device_id' => Yii::t('app', "Device ID"),
            'sms_code' => Yii::t('app', "SMS Code"),
            'type' => Yii::t('app', "Device Type"),
            'auth_token' => Yii::t('app', "Auth Token"),
            'lang' => Yii::t('app', "Language"),
            'created_at' => Yii::t('app', "Created At"),
            'updated_at' => Yii::t('app', "Updated At"),
            'uip' => Yii::t('app', "User IP"),
            'notifications' => Yii::t('app', "Notification Status"),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
