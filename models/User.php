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
 * @property int $approved
 * @property integer $last_activity
 * @property integer $km
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
            ['phone', 'phoneValidate'],
            [[
                'type', 'status', 'gender',
                'image',
                'city_id', 'country_id',
                'email_verified',
                'created_at', 'updated_at', 'blocked_at', 'approval_at',
                'created_by', 'updated_by', 'approval_by', 'blocked_by', 'last_activity',
                'approved'
            ], 'integer'],
            [['email', 'first_name', 'second_name', 'email_confirm_token', 'password_hash', 'password_reset_token', 'auth_key'], 'string', 'max' => 255],
            [['balance', 'phone'], 'number'],
            [[
                'country_id', 'city_id',
                'balance',
                'created_at', 'updated_at', 'blocked_at', 'approval_at',
                'created_by', 'updated_by', 'approval_by', 'blocked_by'
            ], 'default', 'value' => 0],
            ['km', 'number']
        ];
    }

    public function phoneValidate($attribute)
    {
        if (!preg_match('/^[\d]{10,12}$/', $this->$attribute)) {
            $this->addError($attribute, 'Phone must be numeric 10-12 symbols');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'km' => Yii::t('app', "Километры"),
            'phone' => Yii::t('app', "Телефон"),
            'city_id' => Yii::t('app', "Город"),
            'status' => Yii::t('app', "Статус"),
            'first_name' => Yii::t('app', "Имя"),
            'second_name' => Yii::t('app', "Фамилия"),
            'email' => Yii::t('app', "Эл. почта"),
            'gender' => Yii::t('app', "Пол"),
            'image' => Yii::t('app', "Фотограция"),
            'type' => Yii::t('app', "Тип")
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

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByUsername($email)
    {
        return static::findOne(['email' => $email]);
    }

    public function getFullName()
    {
        if (empty ($this->first_name)) $first_name = 'Имя';
        else $first_name = $this->first_name;

        if (empty ($this->second_name)) $second_name = 'Фамилия';
        else $second_name = $this->second_name;

        $name = $first_name . ' ' . $second_name;
        return $name == 'Имя Фамилия' ? 'Не указано' : $name;
    }

    public function getCity()
    {
        return \app\modules\admin\models\City::findOne($this->city_id);
    }

    public function getUserPhoto()
    {
        if ($this->image && intval($this->image) > 0)
        {
            $image = \app\modules\api\models\UploadFiles::findOne($this->image);
            if ($image)
            {
                return Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $image->file, '128x128', ['class' => 'img-circle']);
            }
        }

        return '<img class="img-circle" src="https://placehold.it/128x128" alt="no-photo">';
    }

    public function getOnline()
    {
        if ($this->last_activity == null || $this->last_activity >= time() - 15) return true;

        return false;
    }

    public function getRating($marks = false)
    {
        $rating = (float) 0.0;

        switch ($this->type)
        {
            case self::TYPE_DRIVER: $rating = (float) $this->driverRating($marks);
                break;

            case self::TYPE_PASSENGER: $rating = (float)$this->passengerRating($marks);
                break;
        }

        return $rating;
    }

    protected function driverRating($marks)
    {
        $trips = Trip::find()->andWhere([
            'AND',
            ['=', 'driver_id', $this->id]
        ])->all();

        $rating = 0.0;
        $ratings = 0;
        /** @var \app\models\Trip $trip */
        if ($trips && count ($trips) > 0) foreach ($trips as $trip) if (intval($trip->passenger_rating) > 0)
        {
            $rating += $trip->passenger_rating;
            $ratings++;
        }

        if ($ratings > 0) $rating = round(floatval((float) ($rating / $ratings)), 2);
        if ($marks) return $ratings;
        return $rating;
    }

    protected function passengerRating($marks)
    {
        $trips = Trip::find()->andWhere([
            'AND',
            ['=', 'user_id', $this->id]
        ])->all();

        $rating = 0.0;
        $ratings = 0;
        /** @var \app\models\Trip $trip */
        if ($trips && count ($trips) > 0) foreach ($trips as $trip) if (intval($trip->driver_rating) > 0)
        {
            $rating += $trip->driver_rating;
            $ratings++;
        }

        if ($ratings > 0) $rating = round(floatval((float) ($rating / $ratings)), 2);
        if ($marks) return $ratings;
        return $rating;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ((float) $this->rating < 3 && $this->getRating(true) > 10 && intval($this->blocked_at) != 0)
        {
            $blacklist = Blacklist::find()->where(['user_id' => $this->id])->one();
            if (!$blacklist) $blacklist = new Blacklist();

            $blacklist->user_id = $this->id;
            $blacklist->add_type = Blacklist::TYPE_AUTO;
            $blacklist->status = Blacklist::STATUS_BLACKLISTED;
            $blacklist->add_comment = Yii::$app->params['blacklist']['rating']['comment'];
            $blacklist->description = Yii::$app->params['blacklist']['rating']['description'];
            $blacklist->save();

            Notifications::create(Notifications::NT_BLACKLIST, $this->id, true, Yii::$app->params['blacklist']['rating']['notification']);

            $this->blocked_at = time();
            $this->blocked_reason = Yii::$app->params['blacklist']['rating']['reason'];
            $this->save();
        }
    }

    public function beforeSave($insert)
    {
        if ($this->status == self::STATUS_APPROVED) $this->approved = 1;
        elseif ($this->status == self::STATUS_PENDING || $this->status == self::STATUS_BLOCKED) $this->approved = 0;

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function hasPermission($permissionRole = []) {
        return true;

        $roles = array_keys(Yii::$app->authManager->getRolesByUser($this->id));

        return count($roles) !== count(array_diff($roles, $permissionRole));
    }
}
