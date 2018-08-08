<?php namespace app\models;

use Yii;

/**
 * This is the model class for table "agreement_lang".
 *
 * @property integer $id
 * @property string $content
 * @property integer $original_id
 * @property string $language
 */
class AgreementLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agreement_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'original_id'], 'integer'],
            [['content','title'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'content' => Yii::t('app', 'Содержание'),
            'title' => Yii::t('app', 'Заголовок'),
            'original_id' => Yii::t('app', 'ID записи'),
            'language' => Yii::t('app', 'Язык'),
        ];
    }
}