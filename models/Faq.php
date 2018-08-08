<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;

/**
 * This is the model class for table "faq".
 *
 * @property int $id
 * @property string $title
 * @property int $type
 * @property string $content
 * @property int $created_at
 * @property int $updated_at
 */
class Faq extends \yii\db\ActiveRecord
{
    const
        TYPE_DRIVER = 1,
        TYPE_PASSENGER = 2;

    public static function tableName()
    {
        return 'faq';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Lang::getBehaviorsList(),
                //'languageField' => 'language',
                //'localizedPrefix' => '',
                //'requireTranslations' => false',
                //'dynamicLangClass' => true',
                'defaultLanguage' => Lang::getCurrent()->local,
                'langForeignKey' => 'original_id',
                'tableName' => "{{%faq_lang}}",
                'attributes' => [
                    'content', 'title'
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['type'], 'integer'],
            [['title','content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('app', "ID"),
            'title'         => Yii::t('app', "Заголовок"),
            'content'       => Yii::t('app', "Описание"),
            'type'          => Yii::t('app', "Тип"),
            'created_at'    => Yii::t('app', "Создано"),
            'updated_at'    => Yii::t('app', "Обновлено")
        ];
    }

    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }

    public static function getTypesList()
    {
        return [
            self::TYPE_PASSENGER => Yii::t('app', "Пассажир"),
            self::TYPE_DRIVER => Yii::t('app', "Водитель")
        ];
    }
}
