<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property string $title
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    public static function getCitiesList($asArray = false) {
        $list = self::find()->all();

        $cities = [];
        /** @var \app\models\City $city */
        if ($asArray && $list && count ($list) > 0) foreach ($list as $city) $cities[] = [
            'id' => $city->id,
            'value' => $city->title
        ];

        return $asArray ? ArrayHelper::map($list, 'id', 'title') : $list;
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
                'tableName' => "{{%city_lang}}",
                'attributes' => ['title']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'title' => Yii::$app->mv->gt('Title', [], 0),
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
