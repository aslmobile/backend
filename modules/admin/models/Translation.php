<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Translation extends Model
{
    public $val;
    public $new_val;
    public $isNewRecord = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name and password are both required
            [['val'], 'required'],
            // rememberMe must be a boolean value
            ['val', 'string'],

            [['new_val'], 'required'],
            // rememberMe must be a boolean value
            ['new_val', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'val' => Yii::t('app', 'Translation'),
            'new_val' => Yii::t('app', 'New translation'),
        ];
    }

    /**
     * @return array
     */
    public static function getTranslations()
    {
        $translations = ArrayHelper::map(Translations::find()->asArray()->all(), 'original_val', 'val');

        return $translations;
    }

    public function save()
    {
        $translations = Translations::find()->where(['original_val' => $this->val])->all();
        foreach ($translations as $translation) {
            $translation->val = $this->new_val;
            $translation->save();
        }
    }

}
