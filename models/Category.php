<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $short_description
 * @property string $link
 * @property string $image
 * @property string $small_image
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $sort
 * @property string $file
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 2;

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
                'langForeignKey' => 'original_id',
                'tableName' => "{{%category_lang}}",
                'attributes' => ['title', 'description', 'short_description']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['status'], 'integer'],
            [['title', 'link', 'image'], 'string', 'max' => 255],
            [['small_image'], 'string', 'max' => 255],
            [['short_description'], 'string', 'max' => 1000],
            [['description'], 'string'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['sort'], 'integer'],
            [['file'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'title' => Yii::$app->mv->gt('Title', [], 0),
            'description' => Yii::$app->mv->gt('Description', [], 0),
            'short_description' => Yii::$app->mv->gt('Short Description', [], 0),
            'link' => Yii::$app->mv->gt('Link', [], 0),
            'image' => Yii::$app->mv->gt('Image', [], 0),
            'small_image' => Yii::$app->mv->gt('Small Image', [], 0),
            'status' => Yii::$app->mv->gt('Status', [], 0),
            'created_at' => Yii::$app->mv->gt('Created At', [], 0),
            'updated_at' => Yii::$app->mv->gt('Updated At', [], 0),
            'sort' => Yii::$app->mv->gt('Sorting', [], 0),
            'file' => Yii::$app->mv->gt('File', [], 0),
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::$app->mv->gt('Active', [], false),
            self::STATUS_NOT_ACTIVE => Yii::$app->mv->gt('Hidden', [], false),
        ];
    }

    /**
    * @inheritdoc
    * @return  the active query used by this AR class.
    */
    public static function find()
    {
        $q = new MultilingualQuery(get_called_class());
        $q->localized();
        return $q;
    }

}
