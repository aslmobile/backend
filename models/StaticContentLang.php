<?php

namespace app\models;

use Yii;
/**
* This is the model class for table "static_content_lang".
*
    * @property integer $id
    * @property string $fan_title
    * @property string $fan_tooltip
    * @property string $crush_tooltip
    * @property string $verified_tooltip
    * @property string $unique_code_tooltip
*/
class StaticContentLang extends \yii\db\ActiveRecord
{
    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return 'static_content_lang';
    }

    
    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [
            [['fan_tooltip', 'crush_tooltip', 'verified_tooltip', 'unique_code_tooltip'], 'string'],
            [['fan_title'], 'string', 'max' => 255],
            [['original_id'], 'integer'],
            [['language'], 'string', 'max' => 12]
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
            'fan_tooltip' => Yii::$app->mv->gt('Info',[],0),
            'crush_tooltip' => Yii::$app->mv->gt('Info',[],0),
            'verified_tooltip' => Yii::$app->mv->gt('Info',[],0),
            'unique_code_tooltip' => Yii::$app->mv->gt('Info',[],0),
            'original_id' => Yii::t('app', 'Original ID'),
            'language' => Yii::t('app', 'Language'),
        ];
    }
    }
