<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "dispatch".
 *
 * @property integer $id
 * @property string $name
 * @property double $phone
 * @property integer $created_at
 * @property integer $updated_at
 */
class Dispatch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dispatch';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'number'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'name' => Yii::$app->mv->gt('Name', [], 0),
            'phone' => Yii::$app->mv->gt('Phone', [], 0),
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
}
