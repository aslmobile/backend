<?php

namespace app\models;

use app\modules\api\models\UploadFiles;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $email
 * @property integer $email_verified
 * @property string $email_confirm_token
 * @property integer $type
 * @property string $first_name
 * @property string $second_name
 * @property integer $gender
 * @property integer $city_id
 * @property integer $image
 * @property float $phone
 * @property string $password_hash
 * @property string $password_reset_token
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $approval_at
 * @property integer $approval_by
 * @property integer $blocked_at
 * @property integer $blocked_by
 * @property string $blocked_reason
 * @property string $auth_key
 */
class User extends ActiveRecord implements IdentityInterface
{
    const
        TYPE_ADMIN = 1,
        TYPE_MANAGER = 2,
        TYPE_DRIVER = 3,
        TYPE_PASSENGER = 4;

    public $old_password;
    public $new_password;
    public $repeat_password;
    public $uip;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_BLOCKED = 9;

    public $rids = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('app', 'This email address has already been taken.')
            ],
            ['phone', 'filter', 'filter' => function ($value) {
                return preg_replace('/[^\d]/i', '', $value);
            }],
            ['phone', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('app', 'This phone has already been taken.')
            ],
            [[
                'type', 'status', 'gender',
                'image',
                'city_id', 'country_id',
                'email_verified',
                'created_at', 'updated_at', 'blocked_at', 'approval_at',
                'created_by', 'updated_by', 'approval_by', 'blocked_by'
            ], 'integer'],
            [['balance', 'phone'], 'number'],
            [[
                'country_id', 'city_id',
                'balance',
                'created_at', 'updated_at', 'blocked_at', 'approval_at',
                'created_by', 'updated_by', 'approval_by', 'blocked_by'
            ], 'default', 'value' => 0],
        ];



        return [
            [['phone'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('app', 'This email address has already been taken.')],
            ['phone', 'filter', 'filter' => function ($value) {
                return preg_replace('/[^\d]/i', '', $value);
            }],
            [['phone'], 'number'],
            ['phone', 'unique', 'targetClass' => self::className(), 'message' => Yii::t('app', 'This phone number has already been taken.')],
            [['type', 'gender', 'image', 'city_id', 'email_verified', 'status', 'created_at', 'updated_at', 'blocked_at', 'approval_at', 'created_by', 'updated_by', 'approval_by', 'blocked_by'], 'integer'],
            [['email', 'first_name', 'second_name', 'email_confirm_token', 'password_hash', 'password_reset_token', 'auth_key', 'blocked_reason'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(Yii::$app->params['statuses'])],
            ['status', 'default', 'value' => 1],
            ['gender', 'in', 'range' => array_keys(Yii::$app->params['gender'])],
            ['gender', 'default', 'value' => 2],
            ['type', 'in', 'range' => array_keys(Yii::$app->params['user_type'])],
            ['type', 'default', 'value' => 0],

            [['balance'], 'number'],
            [
                [
                    'country_id', 'city_id',
                    'balance',
                    'blocked_at', 'blocked_by',
                    'approval_at', 'approval_by',
                    'updated_at', 'updated_by',
                    'created_at', 'created_by'
                ],
                'default', 'value' => 0
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function getStatuses()
    {
        return Yii::$app->params['statuses'];
    }

    public static function getTypes()
    {
        return Yii::$app->params['user_type'];
    }

    public static function getGenders()
    {
        return Yii::$app->params['gender'];
    }

    public static function getCountries()
    {
        return ArrayHelper::map(Countries::find()->all(),'id', 'title');
    }

    public function getStatusName()
    {
        $statuses = self::getStatuses();
        return isset($statuses[$this->status]) ? $statuses[$this->status] : '';
    }

    public function getAvatar()
    {
        return $this->imageFile;
    }

    public function getImageFile()
    {
        $file_id = intval($this->image);
        if ($file_id > 0)
        {
            $file = UploadFiles::findOne(['id' => $file_id]);
            if ($file) return $file->file;
        }

        return false;
    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => 1]);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return void|IdentityInterface
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('findIdentityByAccessToken is not implemented.');
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->second_name;
    }
}
