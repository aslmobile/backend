<?php

namespace app\models;

use app\components\MetaBehavior;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "dynamic".
 *
 * @property integer $id
 * @property string $title
 * @property string $image
 * @property string $url
 * @property string $short_text
 * @property string $text
 * @property integer $created_at
 * @property integer $updated_at
 */
class Dynamic extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_HIDE = 2;
    const STATUS_DELETED = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dynamic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['short_text', 'text'], 'string'],
            [['blocks'], 'safe'],
            ['url', 'unique', 'targetClass' => self::className(), 'message' => Yii::$app->mv->gt('URL exists', [], 0)],
            [['created_at', 'updated_at', 'status', 'template'], 'integer'],
            [['title', 'image', 'url', 'subtitle'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'title' => Yii::$app->mv->gt('Заголовок', [], 0),
            'image' => Yii::$app->mv->gt('Баннер', [], 0) . " 1366x165",
            'url' => Yii::$app->mv->gt('Ссылка', [], 0),
            'short_text' => Yii::$app->mv->gt('Краткое описание', [], 0),
            'text' => Yii::$app->mv->gt('Описание', [], 0),
            'created_at' => Yii::$app->mv->gt('Создана', [], 0),
            'updated_at' => Yii::$app->mv->gt('Обнавлена', [], 0),
            'status' => Yii::$app->mv->gt('Статус', [], 0),
            'subtitle' => Yii::$app->mv->gt('Подзаголовок', [], 0),
            'template' => Yii::$app->mv->gt('Шаблон', [], 0),
            'blocks' => Yii::$app->mv->gt('Блоки', [], 0),
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::$app->mv->gt("Активный", [], 0),
            self::STATUS_HIDE => Yii::$app->mv->gt("Скрытый", [], 0),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'ml' => [
                'class' => MultilingualBehavior::className(),
                'languages' => Lang::getBehaviorsList(),
                //'languageField' => 'language',
                //'localizedPrefix' => '',
                //'requireTranslations' => false',
                //'dynamicLangClass' => true',
                'defaultLanguage' => Lang::getCurrent()->local,
                'langForeignKey' => 'dynamic_id',
                'tableName' => "{{%dynamic_lang}}",
                'attributes' => [
                    'title', 'short_text', 'text', 'subtitle'
                ]
            ],
            [
                'class' => SluggableBehavior::className(),
                'slugAttribute' => 'url',
                'attribute' => 'title',
                'immutable' => true,
                'ensureUnique' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return ActiveQuery the active query used by this AR class.
     */
    public static function find($show_delete = false)
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        if (!$show_delete) {
            $q->andWhere(['NOT IN', self::tableName() . '.status', [self::STATUS_DELETED]]);
        }
        return $q;
    }

    public function beforeSave($insert)
    {
        if (!$this->url) {
            $this->url = Yii::$app->mv->transliterateUrl($this->title);
        } else {
            $this->url = Yii::$app->mv->transliterateUrl($this->url);
        }
        /*
        foreach (Lang::getBehaviorsList() as $k => $v) {
            if (!$this->{'url_'.$k}) {
                $this->{'url_'.$k} = Yii::$app->mv->transliterateUrl($this->title);
            }
            else{
                $this->{'url_'.$k} = Yii::$app->mv->transliterateUrl($this->{'url_'.$k});
            }
        }
        foreach (Lang::getBehaviorsList() as $k => $v) {
            $this->{'url_'.$k} = Yii::$app->mv->transliterateUrl($this->{'url_'.$k});
        }
        */
        if ($this->blocks) {
            $this->blocks = serialize($this->blocks);
        }
        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {
        $this->status = 10;
        $this->save(false);

        return false;
    }

}
