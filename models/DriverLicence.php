<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "driver_license".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property integer $status
 * @property integer $type
 * @property string $image
 * @property string $image2
 * @property integer $created_at
 * @property integer $updated_at
 */
class DriverLicence extends ActiveRecord
{
    const
        TYPE_LICENSE = 1,
        TYPE_INSURANCE = 2,
        TYPE_REGISTRATION = 3;

    const
        STATUS_UPLOADED = 1,
        STATUS_APPROVED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'driver_license';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['image'], 'required'],
            [['user_id', 'type', 'status'], 'integer'],
            [['title', 'image', 'image2'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
