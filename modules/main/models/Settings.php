<?php

namespace app\modules\main\models;

use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
use app\modules\admin\models\Lang;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property string $name
 * @property string $descr
 * @property integer $maint
 * @property string $copy
 * @property string $logo
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
            [['name', 'copy'], 'required'],
            [['descr'], 'string'],
            [['maint'], 'integer'],
            [['name', 'copy', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'name' => 'Название сайта',
            'descr' => 'Описание сайта',
            'maint' => 'Режим обслуживания',
            'copy' => 'Копирайт',
            'logo' => 'Логотип',
        ];
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
                'langForeignKey' => 'settings_id',
                'tableName' => "{{%settings_lang}}",
                'attributes' => [
                    'name', 'copy', 'descr',
                ],
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
