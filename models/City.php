<?php

namespace app\models;

use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property integer $status
 * @property string $title
 */
class City extends \yii\db\ActiveRecord
{

    const
        STATUS_DISABLED = 0,
        STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    public static function getCitiesList($asArray = false)
    {
        $list = self::find()->where(['status' => self::STATUS_ACTIVE])->all();

        $cities = [];
        /** @var \app\models\City $city */
        if ($asArray && $list && count($list) > 0) foreach ($list as $city) $cities[] = [
            'id' => $city->id,
            'value' => $city->title
        ];

        if ($asArray === true) return ArrayHelper::map($list, 'id', 'title');
        elseif ($asArray === 2) return $cities;
        return $list;
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
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
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
            'status' => Yii::$app->mv->gt('Статус', [], 0),
            'country_id' => Yii::$app->mv->gt('Страна', [], 0),
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

    public function getCountry(){
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }

}
