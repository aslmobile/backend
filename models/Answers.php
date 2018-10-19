<?php

namespace app\models;

use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "answers".
 *
 * @property integer $id
 * @property integer $type
 * @property string $answer
 * @property integer $created_at
 * @property integer $updated_at
 */
class Answers extends \yii\db\ActiveRecord
{
    const
        TYPE_CTR = 1,
        TYPE_CPR = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answers';
    }

    public function behaviors()
    {
        return [
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Lang::getBehaviorsList(),
                //'languageField' => 'language',
                //'localizedPrefix' => '',
                //'requireTranslations' => false',
                //'dynamicLangClass' => true',
                'defaultLanguage' => Lang::getCurrent()->local,
                'langForeignKey' => 'original_id',
                'tableName' => "{{%answers_lang}}",
                'attributes' => ['answer']
            ],
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            ['type', 'unique', 'targetClass' => self::class, 'message' => Yii::t('app', "Данный тип уже занят")],
            [['answer'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'type' => Yii::$app->mv->gt('Тип', [], 0),
            'answer' => Yii::$app->mv->gt('Ответы', [], 0),
            'created_at' => Yii::$app->mv->gt('Создано', [], 0),
            'updated_at' => Yii::$app->mv->gt('Обновлено', [], 0),
        ];
    }

    /**
     * @return MultilingualQuery|\yii\db\ActiveQuery
     */
    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }

    public static function getTypesList()
    {
        return [
            null => Yii::t('app', "Не определено"),
            self::TYPE_CPR => Yii::t('app', "Водитель. Отмена поездки"),
            self::TYPE_CTR => Yii::t('app', "Пассажир. Отмена поездки."),
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
