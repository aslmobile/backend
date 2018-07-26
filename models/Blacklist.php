<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "blacklist".
 *
 * @property integer $id
 * @property string $add_comment
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $user_id
 * @property integer $add_type
 * @property string $description
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $cancel_comment
 */
class Blacklist extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blacklist';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'status', 'user_id', 'add_type', 'created_by', 'updated_by'], 'integer'],
            [['add_comment', 'cancel_comment'], 'string', 'max' => 1000],
            [['description'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'add_comment' => Yii::$app->mv->gt('Add Comment', [], 0),
            'created_at' => Yii::$app->mv->gt('Created At', [], 0),
            'updated_at' => Yii::$app->mv->gt('Updated At', [], 0),
            'status' => Yii::$app->mv->gt('Status', [], 0),
            'user_id' => Yii::$app->mv->gt('User ID', [], 0),
            'add_type' => Yii::$app->mv->gt('Add Type', [], 0),
            'description' => Yii::$app->mv->gt('Description', [], 0),
            'created_by' => Yii::$app->mv->gt('Created By', [], 0),
            'updated_by' => Yii::$app->mv->gt('Updated By', [], 0),
            'cancel_comment' => Yii::$app->mv->gt('Cancel Comment', [], 0),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}