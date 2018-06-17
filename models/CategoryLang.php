<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "category_lang".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $short_description
 */
class CategoryLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_lang';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255],
            [['short_description'], 'string', 'max' => 500],
            [['language'], 'string', 'max' => 12],
            [['description'], 'string'],
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
            'short_description' => Yii::$app->mv->gt('Short Description', [], 0),
            'description' => Yii::$app->mv->gt('Description', [], 0),
            'original_id' => Yii::t('app', 'Original ID'),
            'language' => Yii::t('app', 'Language'),
        ];
    }

}
