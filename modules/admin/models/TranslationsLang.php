<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "translations_lang".
 *
 * @property integer $id
 * @property integer $translations_id
 * @property string $language
 * @property string $val
 */
class TranslationsLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'translations_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['translations_id', 'language', 'val'], 'required'],
            [['translations_id'], 'integer'],
            [['val'], 'string'],
            [['language'], 'string', 'max' => 6]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'translations_id' => Yii::t('app', 'Translations ID'),
            'language' => Yii::t('app', 'Language'),
            'val' => Yii::t('app', 'Val'),
        ];
    }
}
