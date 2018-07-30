<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "answers".
 *
 * @property integer $id
 * @property integer $type
 * @property string $answer
 * @property integer $created_at
 * @property integer $updated_at
 */
class Answers extends \yii\db\ActiveRecord
{
    const
        TYPE_CTR = 1,
        TYPE_CPR = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answers';
    }


    public function behaviors()
    {
        return [
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Lang::getBehaviorsList(),
                //'languageField' => 'language',
                //'localizedPrefix' => '',
                //'requireTranslations' => false',
                //'dynamicLangClass' => true',
                'defaultLanguage' => Lang::getCurrent()->local,
                'langForeignKey' => 'original_id',
                'tableName' => "{{%answers_lang}}",
                'attributes' => ['answer',]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['answer'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'type' => Yii::$app->mv->gt('Type', [], 0),
            'answer' => Yii::$app->mv->gt('Answer', [], 0),
            'created_at' => Yii::$app->mv->gt('Created At', [], 0),
            'updated_at' => Yii::$app->mv->gt('Updated At', [], 0),
        ];
    }

    /**
     * @inheritdoc
     * @return  the active query used by this AR class.
     */
    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }

}