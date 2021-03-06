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
 * @property string $title
 * @property int $status
 * @property string $alpha2
 * @property string $alpha3
 * @property int $created_at
 * @property int $updated_at
 */
class Countries extends \yii\db\ActiveRecord
{
    const
        STATUS_ACTIVE = 1,
        STATUS_DISABLED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
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
            [['title'], 'required'],
            [['title'], 'string', 'max' => 60],
            [['alpha2'], 'string', 'max' => 2],
            [['alpha3'], 'string', 'max' => 3],
            [['status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'title' => Yii::t('app', "Название"),
            'alpha2' => Yii::t('app', "ISO Alpha2"),
            'alpha3' => Yii::t('app', "ISO Alpha3")
        ];
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
