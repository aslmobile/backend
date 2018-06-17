<?php

namespace app\models;

use Yii;

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
            [['settings_id'], 'integer'],
            [['language'], 'string', 'max' => 6],
            [['name', 'copy'], 'string', 'max' => 255],
            [['title','description','keywords','social','descr','address'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID',[],false),
            'name' => Yii::$app->mv->gt('ID',[],false),
            'descr' => Yii::$app->mv->gt('Description',[],false),
            'copy' => Yii::$app->mv->gt('Copyright',[],false),
            'address' => Yii::$app->mv->gt('Address',[],false),
            'title' => Yii::$app->mv->gt('Default page title',[],false),
            'description' => Yii::$app->mv->gt('Default page description',[],false),
            'keywords' => Yii::$app->mv->gt('Default page keywords',[],false),
            'social' => Yii::$app->mv->gt('Social links',[],false),
        ];
    }
}
