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
 * @property integer $app
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
        NOTIFICATION_DISABLED = 0,
        NOTIFICATION_ENABLED = 1;


    const
        APP_DRIVER = 1,
        APP_PASSENGER = 2;

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
            [['user_id', 'type', 'user_type', 'notifications', 'app'], 'integer'],
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
            'user_id' => Yii::t('app', "Пользователь"),
            'user_type' => Yii::t('app', "Тип пользователя"),
            'push_id' => Yii::t('app', "ID Пуша"),
            'device_id' => Yii::t('app', "ID Девайса"),
            'sms_code' => Yii::t('app', "Код авторизации"),
            'type' => Yii::t('app', "Теп девайса"),
            'auth_token' => Yii::t('app', "Токен авторизации"),
            'lang' => Yii::t('app', "Язык"),
            'created_at' => Yii::t('app', "Создан"),
            'updated_at' => Yii::t('app', "Обновлен"),
            'uip' => Yii::t('app', "IP Пользователя"),
            'notifications' => Yii::t('app', "Статус нотификаций"),
            'app' => Yii::t('app', "Тип приложения"),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
