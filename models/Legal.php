<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;

/**
 * This is the model class for table "legal".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $type
 * @property int $created_at
 * @property int $updated_at
 */
class Legal extends \yii\db\ActiveRecord
{
    const
        STATUS_ADDED = 0,
        STATUS_APPROVED = 1,
        STATUS_WAITING = 2;

    const
        TYPE_DRIVER = 1,
        TYPE_PASSENGER = 2;

    public static function tableName()
    {
        return 'legal';
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
                'tableName' => "{{%legal_lang}}",
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
            [['title', 'type'], 'required'],
            [['title'], 'string'],
            ['content', 'safe'],
            [['type'], 'integer']
        ];
    }

    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', "ID"),
            'title'             => Yii::t('app', "Заголовок"),
            'content'           => Yii::t('app', "Содержание"),
            'type'              => Yii::t('app', "Тип"),
            'weight'            => Yii::t('app', "Вес (сортировка)"),
            'created_at'        => Yii::t('app', "Создан"),
            'updated_at'        => Yii::t('app', "Обновлен")
        ];
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    public static function getTypesList()
    {
        return [
            self::TYPE_PASSENGER => Yii::t('app', "Пассажир"),
            self::TYPE_DRIVER => Yii::t('app', "Водитель")
        ];
    }
}
