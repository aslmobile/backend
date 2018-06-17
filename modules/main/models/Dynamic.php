<?php

namespace app\modules\main\models;

use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
use app\modules\admin\models\Lang;
use yii\behaviors\TimestampBehavior;

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
            [['title', 'url', 'short_text', 'text'], 'required'],
            [['short_text', 'text'], 'string'],
            ['url', 'unique', 'targetClass' => self::className(), 'message' => 'Данная сслыка уже занята'],
            [['created_at', 'updated_at'], 'integer'],
            [['title', 'image', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'title' => 'Заголовок',
            'image' => 'Картинка',
            'url' => 'Ссылка',
            'short_text' => 'Краткий текст',
            'text' => 'Текст',
            'created_at' => 'Создана',
            'updated_at' => 'Изменена',
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
                    'title', 'short_text', 'text',
                ],
            ],
        ];
    }

    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();

        return $q;
    }
}
