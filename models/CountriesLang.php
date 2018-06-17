<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "countries_lang".
 *
 * @property integer $id
 * @property integer $original_id
 * @property string $language
 * @property string $title
 */
class CountriesLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'countries_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['original_id', 'language', 'title'], 'required'],
            [['original_id'], 'integer'],
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
            'original_id' => Yii::$app->mv->gt('Country ID', [], 0),
            'language' => Yii::$app->mv->gt('Language', [], 0),
            'title' => Yii::$app->mv->gt('Title', [], 0),
        ];
    }
}
