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
                'langForeignKey' => 'legal_id',
                'tableName' => "{{%legal_lang}}",
                'attributes' => [
                    'content'
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
            [['title', 'content'], 'string'],
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
            'title'             => Yii::t('app', "Title"),
            'content'           => Yii::t('app', "Content"),
            'type'              => Yii::t('app', "Type"),
            'created_at'        => Yii::t('app', "Created"),
            'updated_at'        => Yii::t('app', "Updated")
        ];
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }
}
