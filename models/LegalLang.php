<?php namespace app\models;

use Yii;

/**
 * This is the model class for table "legal_lang".
 *
 * @property integer $id
 * @property string $content
 * @property integer $legal_id
 * @property string $language
 */
class LegalLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'legal_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'legal_id'], 'integer'],
            [['content'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'content' => Yii::t('app', 'Content'),
            'legal_id' => Yii::t('app', 'Legal ID'),
            'language' => Yii::t('app', 'Language'),
        ];
    }
}
