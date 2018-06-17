<?php

namespace app\modules\main\models;

/**
 * This is the model class for table "settings_lang".
 *
 * @property integer $id
 * @property integer $settings_id
 * @property string $language
 * @property string $name
 * @property string $copy
 */
class SettingsLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['settings_id', 'language', 'name', 'copy'], 'required'],
            [['settings_id'], 'integer'],
            [['language'], 'string', 'max' => 6],
            [['name', 'copy'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'settings_id' => 'Settings ID',
            'language' => 'Language',
            'name' => 'Name',
            'copy' => 'Copy',
        ];
    }
}
