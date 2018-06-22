<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "country".
 *
 * @property integer $id
 * @property string $title_ru
 * @property string $title_ua
 * @property string $title_be
 * @property string $title_en
 * @property string $title_es
 * @property string $title_pt
 * @property string $title_de
 * @property string $title_fr
 * @property string $title_it
 * @property string $title_po
 * @property string $title_ja
 * @property string $title_lt
 * @property string $title_lv
 * @property string $title_cz
 * @property string $title_zh
 * @property string $title_he
 * @property string $code_alpha2
 * @property string $code_alpha3
 * @property integer $code_iso
 * @property string $flag
 * @property integer $dc
 */
class Countries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'countries';
    }

    public static function getCountiesList($asArray = false){
        $list = self::find()->asArray($asArray)->all();
        return $asArray ? ArrayHelper::map($list, 'id', 'title_ru') : $list;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code_iso', 'title_en'], 'required'],
            [['code_iso', 'dc'], 'integer'],
            [['title_ru', 'title_be', 'title_en', 'title_es', 'title_pt', 'title_de', 'title_fr', 'title_it', 'title_po', 'title_ja', 'title_lt', 'title_lv', 'title_cz', 'title_zh', 'title_he'], 'string', 'max' => 60],
            [['code_alpha2'], 'string', 'max' => 2],
            [['code_alpha3'], 'string', 'max' => 3],
            [['flag'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt("ID",[],0),
            'title_ru' => Yii::$app->mv->gt("Title",[],0)." ru",
            'title_ua' => Yii::$app->mv->gt("Title",[],0)." ua",
            'title_be' => Yii::$app->mv->gt("Title",[],0)." be",
            'title_en' => Yii::$app->mv->gt("Title",[],0),
            'title_es' => Yii::$app->mv->gt("Title",[],0)." es",
            'title_pt' => Yii::$app->mv->gt("Title",[],0)." pt",
            'title_de' => Yii::$app->mv->gt("Title",[],0)." de",
            'title_fr' => Yii::$app->mv->gt("Title",[],0)." fr",
            'title_it' => Yii::$app->mv->gt("Title",[],0)." it",
            'title_po' => Yii::$app->mv->gt("Title",[],0)." po",
            'title_ja' => Yii::$app->mv->gt("Title",[],0)." ja",
            'title_lt' => Yii::$app->mv->gt("Title",[],0)." lt",
            'title_lv' => Yii::$app->mv->gt("Title",[],0)." lv",
            'title_cz' => Yii::$app->mv->gt("Title",[],0)." cz",
            'title_zh' => Yii::$app->mv->gt("Title",[],0)." zh",
            'title_he' => Yii::$app->mv->gt("Title",[],0)." he",
            'code_iso' => Yii::$app->mv->gt("ISO code",[],0),
            'code_alpha2' => Yii::$app->mv->gt("ISO code alpha2",[],0),
            'code_alpha3' => Yii::$app->mv->gt("ISO code alpha3",[],0),
            'flag' => Yii::$app->mv->gt("Flag",[],0),
            //'dc' => 'Dc',
        ];
    }

    public function getTitle(){
        if(!Yii::$app->language){
            $field_l = 'en';
        }
        else{
            $field_l = explode("-",Yii::$app->language)[0];
        }
        return $this->{'title_'.$field_l};
    }

    public static function filter($first_empty = true) {
        $key = 'countries_list_'.Yii::$app->language.'_'.intval($first_empty);
        if(!$list = Yii::$app->cache->get($key)){
            $list = [];
            if($first_empty){
                $list[''] = 'Country';
            }
            $countries_list = self::find()->all();
            $list = ArrayHelper::merge($list, ArrayHelper::map($countries_list, 'id', 'title'));

            Yii::$app->cache->set($key, $list);
        }

        return $list;
    }

}
