<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mailtpl_lang".
 *
 * @property integer $id
 * @property string $title
 * @property string $descr
 * @property integer $mailtpl_id
 * @property string $language
 */
class MailtplLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailtpl_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'mailtpl_id'], 'integer'],
            [['descr'], 'string'],
            [['title'], 'string', 'max' => 255],
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
            'title' => Yii::t('app', 'Title'),
            'descr' => Yii::t('app', 'Descr'),
            'mailtpl_id' => Yii::t('app', 'Mailtpl ID'),
            'language' => Yii::t('app', 'Language'),
        ];
    }
}
