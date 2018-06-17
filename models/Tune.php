<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "tune".
 *
 * @property integer $id
 * @property string $type
 * @property string $val
 */
class Tune extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tune';
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
                'tableName' => "{{%tune_lang}}",
                'attributes' => ['val']
            ],
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['val','widget'], 'string'],
            [['type'], 'string', 'max' => 255],
            ['widget','default','value'=>'textInput']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'type' => Yii::$app->mv->gt('Type',[],false),
            'val' => Yii::$app->mv->gt('Value',[],false),
            'widget' => Yii::$app->mv->gt('Widget',[],false),
        ];
    }
	
	public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }
	
	public function afterSave($insert, $changedAttributes){
		parent::afterSave($insert, $changedAttributes);
		$langs = Lang::find()->all();
		foreach($langs as $l){
			Yii::$app->cache->set('tune_'.$l->local, false);
		}
	}
	
}
