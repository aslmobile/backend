<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "sms_templates".
 *
 * @property integer $id
 * @property string $template
 * @property string $name
 * @property integer $created_at
 * @property integer $updated_at
 */
class SmsTemplates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_templates';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template', 'name'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'template' => Yii::$app->mv->gt('Шаблон', [], 0),
            'name' => Yii::$app->mv->gt('Название', [], 0),
            'created_at' => Yii::$app->mv->gt('Создан', [], 0),
            'updated_at' => Yii::$app->mv->gt('Обновлен', [], 0),
        ];
    }

}
