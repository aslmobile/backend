<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;

/**
 * This is the model class for table "mailtpl".
 *
 * @property integer $id
 * @property string $title
 * @property string $type
 * @property string $descr
 */
class Mailtpl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailtpl';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['descr'], 'string'],
            [['title', 'type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    
    public function behaviors()
    {
        return [
            //TimestampBehavior::className(),
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Lang::getBehaviorsList(),
                //'languageField' => 'language',
                //'localizedPrefix' => '',
                //'requireTranslations' => false',
                //'dynamicLangClass' => true',
                'defaultLanguage' => Lang::getCurrent()->local,
                'langForeignKey' => 'mailtpl_id',
                'tableName' => "{{%mailtpl_lang}}",
                'attributes' => [
                    'title', 'descr'
                ]
            ],
        ];
    }

    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Заголовок письма'),
            'descr' => Yii::t('app', 'Тело письма'),
            'type' => Yii::t('app', 'Тип'),
        ];
    }

    const
        TYPE_DEFAULT = 'default';

    public static function getTypeList()
    {
        return [
            self::TYPE_DEFAULT => Yii::t('app', "Стандартный")
        ];
    }
}
