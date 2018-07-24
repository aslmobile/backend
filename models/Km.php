<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "km".
 *
 * @property integer $id
 * @property string $settings
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 */
class Km extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'km';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['settings', 'description'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'settings' => Yii::t('app', 'Настрйоки'),
            'description' => Yii::t('app', 'Описание'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
