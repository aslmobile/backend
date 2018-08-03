<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "trip_luggage".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $need_place
 * @property integer $seats
 * @property string $unique_id
 * @property double $amount
 * @property string $currency
 * @property integer $luggage_type
 */
class TripLuggage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trip_luggage';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'status', 'need_place', 'seats', 'luggage_type'], 'integer'],
            [['unique_id'], 'string'],
            [['amount'], 'number'],
            [['currency'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'created_at' => Yii::$app->mv->gt('Создан', [], 0),
            'updated_at' => Yii::$app->mv->gt('Обновлен', [], 0),
            'status' => Yii::$app->mv->gt('Статус', [], 0),
            'need_place' => Yii::$app->mv->gt('Занимает место', [], 0),
            'seats' => Yii::$app->mv->gt('Мест', [], 0),
            'unique_id' => Yii::$app->mv->gt('Уникальный идентификатор', [], 0),
            'amount' => Yii::$app->mv->gt('Сумма', [], 0),
            'currency' => Yii::$app->mv->gt('Валюта', [], 0),
            'luggage_type' => Yii::$app->mv->gt('Тип багажа', [], 0),
        ];
    }

    public function getLuggageType()
    {
        return LuggageType::findOne($this->luggage_type);
    }
}
