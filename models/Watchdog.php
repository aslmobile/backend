<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "watchdog".
 *
 * @property integer $id
 * @property integer $type
 * @property string $message
 * @property string $baggage
 * @property string $uip
 * @property integer $created_at
 * @property integer $updated_at
 */
class Watchdog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'watchdog';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['message', 'baggage'], 'string'],
            [['uip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id'),
            'type' => Yii::t('app', 'Тип'),
            'message' => Yii::t('app', 'Сообщение'),
            'baggage' => Yii::t('app', 'Baggage'),
            'uip' => Yii::t('app', 'IP'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата обновления'),
        ];
    }

}
