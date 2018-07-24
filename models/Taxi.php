<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "taxi".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $status
 * @property string $address
 * @property integer $checkpoint
 * @property integer $created_at
 * @property integer $updated_at
 */
class Taxi extends \yii\db\ActiveRecord
{
    const
        STATUS_NEW = 0,
        STATUS_PROCESSING = 1,
        STATUS_DONE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'taxi';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'checkpoint', 'status', 'created_at', 'updated_at'], 'integer'],
            [['address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'user_id' => Yii::$app->mv->gt('User ID', [], 0),
            'status' => Yii::$app->mv->gt('Status', [], 0),
            'address' => Yii::$app->mv->gt('Address', [], 0),
            'checkpoint' => Yii::$app->mv->gt('Checkpoint', [], 0),
            'created_at' => Yii::$app->mv->gt('Created At', [], 0),
            'updated_at' => Yii::$app->mv->gt('Updated At', [], 0),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_NEW => Yii::t('app', "Новый"),
            self::STATUS_PROCESSING => Yii::t('app', "В процессе"),
            self::STATUS_DONE => Yii::t('app', "Завершен")
        ];
    }
}
