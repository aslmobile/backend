<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "answers_lang".
 *
 * @property integer $id
 * @property string $answer
 */
class AnswersLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answers_lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['answer'], 'safe'],
            [['original_id'], 'integer'],
            [['language'], 'string', 'max' => 12]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'answer' => 'Answer',
            'original_id' => Yii::t('app', 'Original ID'),
            'language' => Yii::t('app', 'Language'),
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->answer = is_string($this->answer) ? json_decode($this->answer, true) : $this->answer;
    }

    public function beforeSave($insert)
    {
        $this->answer = json_encode($this->answer);
        return parent::beforeSave($insert);
    }

}
