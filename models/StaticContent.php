<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "static_content".
 *
 * @property integer $id
 * @property string $fan_title
 * @property string $fan_title_color
 * @property string $fan_icon
 * @property string $fan_image
 * @property string $fan_tooltip
 * @property string $crush_tooltip
 * @property string $verified_tooltip
 * @property string $unique_code_tooltip
 */
class StaticContent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'static_content';
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
                'tableName' => "{{%static_content_lang}}",
                'attributes' => ['fan_title','fan_tooltip','crush_tooltip','verified_tooltip','unique_code_tooltip',]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fan_tooltip', 'crush_tooltip', 'verified_tooltip', 'unique_code_tooltip'], 'string'],
            [['fan_title', 'fan_icon', 'fan_image'], 'string', 'max' => 255],
            [['fan_title_color'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID',[],0),
            'fan_title' => Yii::$app->mv->gt('Title',[],0),
            'fan_title_color' => Yii::$app->mv->gt('Title Color',[],0),
            'fan_icon' => Yii::$app->mv->gt('Icon',[],0),
            'fan_image' => Yii::$app->mv->gt('Image',[],0),
            'fan_tooltip' => Yii::$app->mv->gt('Info',[],0),
            'crush_tooltip' => Yii::$app->mv->gt('Info',[],0),
            'verified_tooltip' => Yii::$app->mv->gt('Info',[],0),
            'unique_code_tooltip' => Yii::$app->mv->gt('Info',[],0),
        ];
    }

    /**
    * @inheritdoc
    * @return  the active query used by this AR class.
    */
    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }

}
