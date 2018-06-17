<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "city_lang".
 *
 * @property integer $id
 * @property string $language
 * @property integer $original_id
 * @property string $title
 */
class CityLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city_lang';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['original_id'], 'integer'],
            [['language'], 'string', 'max' => 255],
            [['title'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'language' => Yii::$app->mv->gt('Language', [], 0),
            'original_id' => Yii::$app->mv->gt('Original ID', [], 0),
            'title' => Yii::$app->mv->gt('Title', [], 0),
        ];
    }

}
