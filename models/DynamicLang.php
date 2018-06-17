<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dynamic_lang".
 *
 * @property integer $id
 * @property integer $dynamic_id
 * @property string $language
 * @property string $title
 * @property string $short_text
 * @property string $text
 */
class DynamicLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dynamic_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dynamic_id', 'language', 'title'], 'required'],
            [['dynamic_id'], 'integer'],
            [['short_text', 'text','subtitle'], 'string'],
            [['language'], 'string', 'max' => 6],
            [['title','subtitle'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dynamic_id' => Yii::$app->mv->gt('Dynamic ID',[],0),
            'language' => Yii::$app->mv->gt('Language',[],0),
            'title' => Yii::$app->mv->gt('Title',[],0),
            'short_text' => Yii::$app->mv->gt('Short text',[],0),
            'text' => Yii::$app->mv->gt('Text',[],0),
        ];
    }
}
