<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "metadata".
 *
 * @property integer $id
 * @property integer $data_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $data_type
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $bagagge
 */
class Metadata extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'metadata';
    }


    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Lang::getBehaviorsList(),
                //'languageField' => 'language',
                //'localizedPrefix' => '',
                //'requireTranslations' => false',
                //'dynamicLangClass' => true',
                'defaultLanguage' => Lang::getCurrent()->local,
                'langForeignKey' => 'original_id',
                'tableName' => "{{%metadata_lang}}",
                'attributes' => ['title','description','keywords']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_id', 'created_at', 'updated_at', 'data_type'], 'integer'],
            [['bagagge'], 'string'],
            [['title'], 'string', 'max' => 512],
            [['description','keywords'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('id',[],0),
            'data_id' => Yii::$app->mv->gt('data id',[],0),
            'created_at' => Yii::$app->mv->gt('Created',[],0),
            'updated_at' => Yii::$app->mv->gt('Updated',[],0),
            'data_type' => Yii::$app->mv->gt('Data type',[],0),
            /*
            0 - page
            1 - news
            2 - child
            3 - projects
            4 - report
            5 - team
            6 - 
            */
            'title' => Yii::$app->mv->gt('Title',[],0),
            'description' => Yii::$app->mv->gt('Description',[],0),
            'bagagge' => Yii::$app->mv->gt('Bagagge',[],0),
            'keywords' => Yii::$app->mv->gt('Keywords',[],0),
        ];
    }

    /**
    * @inheritdoc
    * @return ActiveQuery the active query used by this AR class.
    */
    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }





}
