<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "lang".
 *
 * @property integer $id
 * @property string $flag
 * @property string $url
 * @property string $local
 * @property string $name
 * @property integer $default
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $code
 */
class Lang extends \yii\db\ActiveRecord
{

	static $current = null;
	static $_default = null;
	static $behaviors_list = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lang';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['flag', 'name', 'url', 'local', 'code'], 'required'],
            [['default', 'created_at', 'updated_at'], 'integer'],
            [['flag', 'name'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 6],
            [['local'], 'string', 'max' => 12],
            [['code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt("ID",[],0),
            'flag' => Yii::$app->mv->gt("Иконка",[],0),
            'url' => Yii::$app->mv->gt("URL",[],0),
            'local' => Yii::$app->mv->gt("Локаль",[],0),
            'name' => Yii::$app->mv->gt("Название",[],0),
            'default' => Yii::$app->mv->gt("Стандартный",[],0),
            'created_at' => Yii::$app->mv->gt("Создан",[],0),
            'updated_at' => Yii::$app->mv->gt("Обновлен",[],0),
            'code' => Yii::$app->mv->gt("ISO 639-1",[],0),
        ];
    }

	public function behaviors()
   {
       return [
           TimestampBehavior::className(),
       ];
   }

   static function getCurrent()
   {
       if (self::$current === null) {
           self::$current = self::getDefaultLang();
       }
       return self::$current;
   }

   static function setCurrent($url = null)
   {
       $language = self::getLangByUrl($url);
       self::$current = ($language === null) ? self::getDefaultLang() : $language;
       Yii::$app->language = self::$current->local;
   }

   static function getLangs($asArray = false){
        return self::find()->asArray($asArray)->all();
   }

   static function getDefaultLang()
   {
       if (self::$_default === null) {
           self::$_default = Lang::find()->where(['default' => 1])->one();
       }
       return self::$_default;
   }

   static function getLangByUrl($url = null)
   {
       if ($url === null) {
           return null;
       } else {
           $language = Lang::find()->where('url = :url', [':url' => $url])->one();
           if ($language === null) {
               return null;
           } else {
               return $language;
           }
       }
   }

   static function getBehaviorsList()
   {
       if (self::$behaviors_list === null) {
           $list = ArrayHelper::map(self::find()->where('lang.default = 0')->all(), 'local', 'flag');
           $result = array();
           foreach ($list as $k => $v) {
               $parts = explode('-', $k);
               $result[$parts[0]] = Yii::$app->imageCache->img( Yii::getAlias('@webroot') .$v,'18x18', ['class'=>'img-circle']);
           }
           self::$behaviors_list = (count($result))?$result:$list;
       }
       return self::$behaviors_list;
   }

}
