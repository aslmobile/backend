<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property string $name
 * @property string $descr
 * @property integer $maint
 * @property string $copy
 * @property string $logo
 * @property string $telegram
 * @property string $maint_img
 * @property string $site_email
 * @property string $add_email
 * @property int $driver_commission
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'copy', 'logo'], 'required'],
            [['descr','address','phone','addphone','title','description','keywords','head_scripts','body_scripts','end_scripts','social'], 'string'],
            [['maint','syscache', 'driver_commission', 'passenger_fine'], 'integer'],
            [['site_email'], 'email'],
            [['name', 'copy', 'logo','logo_int', 'logo_small'], 'string', 'max' => 255],
            [['site_email'], 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID',[],false),
            'name' => Yii::$app->mv->gt('Название сайта',[],false),
            'descr' => Yii::$app->mv->gt('Описание',[],false),
            'maint' => Yii::$app->mv->gt('Режим обслуживания',[],false),
            'copy' => Yii::$app->mv->gt('Авторское право',[],false),
            'logo' => Yii::$app->mv->gt('Логотип',[],false),
            'logo_int' => Yii::$app->mv->gt('Logo internal',[],false),
            'logo_small' => Yii::$app->mv->gt('Логотип для футера',[],false),
            'site_email' => Yii::$app->mv->gt('Email сайта',[],false),
            'address' => Yii::$app->mv->gt('Адрес',[],false),
            'phone' => Yii::$app->mv->gt('Телефон',[],false),
            'title' => Yii::$app->mv->gt('Заголовок по умолчанию',[],false),
            'description' => Yii::$app->mv->gt('Описание по умолчанию',[],false),
            'keywords' => Yii::$app->mv->gt('Ключевые слова по умолчанию',[],false),
            'head_scripts' => Yii::$app->mv->gt('Скрипты в шапке',[],false),
            'body_scripts' => Yii::$app->mv->gt('Скрипты в теле',[],false),
            'end_scripts' => Yii::$app->mv->gt('Скрипты в подвале',[],false),
            'social' => Yii::$app->mv->gt('Ссылки на соц. сети',[],false),
            'telegram' => Yii::$app->mv->gt('Канал Телеграм',[],false),
            'addphone' => Yii::$app->mv->gt('Дополнительный телефон',[],false),
            'syscache' => Yii::$app->mv->gt('Кеш',[],false),
            'google_api_key' => Yii::$app->mv->gt('Google API ключ',[],false),
            'add_email' => Yii::$app->mv->gt('Дополнительный Email',[],false),
            'copyright' => Yii::$app->mv->gt('Авторское право',[],false),
            'driver_commission' => Yii::$app->mv->gt('Комиссия водителя',[],false),
            'passenger_fine' => Yii::$app->mv->gt('Штраф',[],false),

        ];
    }

    public function behaviors()
    {
        return [
            'ml' => [
                'class' => MultilingualBehavior::class,
                'languages' => Lang::getBehaviorsList(),
                //'languageField' => 'language',
                //'localizedPrefix' => '',
                //'requireTranslations' => false',
                //'dynamicLangClass' => true',
                'defaultLanguage' => Lang::getCurrent()->local,
                'langForeignKey' => 'settings_id',
                'tableName' => "{{%settings_lang}}",
                'attributes' => ['name', 'copy', 'descr','title','description','keywords','social','address']
            ],
        ];
    }

    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }

}
