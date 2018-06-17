<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "tune_lang".
 *
 * @property integer $id
 * @property string $val
 * @property integer $original_id
 * @property string $language
 */
class TuneLang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tune_lang';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['val'], 'string'],
            [['original_id'], 'integer'],
            [['language'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'val' => 'Val',
            'original_id' => 'Original ID',
            'language' => 'Language',
        ];
    }

}
