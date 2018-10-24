<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transaction_log".
 *
 * @property integer $id
 * @property integer $transaction_id
 * @property string $driver
 * @property string $action
 * @property string $request
 * @property string $response
 * @property integer $error_code
 * @property string $error_message
 * @property integer $created_at
 * @property integer $updated_at
 */
class TransactionLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transaction_log';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['transaction_id', 'error_code', 'created_at', 'updated_at'], 'integer'],
            [['request', 'response'], 'string'],
            [['driver', 'action', 'error_message'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'transaction_id' => Yii::$app->mv->gt('Transaction ID', [], 0),
            'driver' => Yii::$app->mv->gt('Driver', [], 0),
            'action' => Yii::$app->mv->gt('Action', [], 0),
            'request' => Yii::$app->mv->gt('Request', [], 0),
            'response' => Yii::$app->mv->gt('Response', [], 0),
            'error_code' => Yii::$app->mv->gt('Error Code', [], 0),
            'error_message' => Yii::$app->mv->gt('Error Message', [], 0),
            'created_at' => Yii::$app->mv->gt('Created At', [], 0),
            'updated_at' => Yii::$app->mv->gt('Updated At', [], 0),
        ];
    }

}
