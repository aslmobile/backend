<?php

namespace app\modules\main\models;

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
            [['dynamic_id', 'language', 'title', 'short_text', 'text'], 'required'],
            [['dynamic_id'], 'integer'],
            [['short_text', 'text'], 'string'],
            [['language'], 'string', 'max' => 6],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dynamic_id' => 'Dynamic ID',
            'language' => 'Language',
            'title' => 'Title',
            'short_text' => 'Short Text',
            'text' => 'Text',
        ];
    }
}
