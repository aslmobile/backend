<?php

namespace app\models;

use app\components\UserBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "feedback".
 *
 * @property integer $id
 * @property integer $viewed
 * @property string $name
 * @property string $email
 * @property string $message
 * @property string $manager_comment
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedback';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            UserBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'message'], 'required'],
            [['viewed', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['message'], 'string'],
            [['email'], 'email'],
            [['name', 'email'], 'string', 'max' => 255],
            [['manager_comment'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt(Yii::t('app', 'ID'), [], 0),
            'viewed' => Yii::$app->mv->gt(Yii::t('app', 'Viewed'), [], 0),
            'name' => Yii::$app->mv->gt(Yii::t('app', 'Name'), [], 0),
            'email' => Yii::$app->mv->gt(Yii::t('app', 'Email'), [], 0),
            'message' => Yii::$app->mv->gt(Yii::t('app', 'Message'), [], 0),
            'manager_comment' => Yii::$app->mv->gt(Yii::t('app', 'Manager Comment'), [], 0),
            'created_at' => Yii::$app->mv->gt(Yii::t('app', 'Created At'), [], 0),
            'updated_at' => Yii::$app->mv->gt(Yii::t('app', 'Updated At'), [], 0),
            'created_by' => Yii::$app->mv->gt(Yii::t('app', 'Created By'), [], 0),
            'updated_by' => Yii::$app->mv->gt(Yii::t('app', 'Updated By'), [], 0),
        ];
    }

}
