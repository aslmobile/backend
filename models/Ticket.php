<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "ticket".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $user_id
 * @property integer $updated_by
 * @property double $amount
 * @property integer $transaction_id
 */
class Ticket extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at', 'created_by', 'user_id', 'updated_by', 'transaction_id'], 'integer'],
            [['amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'status' => Yii::$app->mv->gt('Status', [], 0),
            'created_at' => Yii::$app->mv->gt('Created At', [], 0),
            'updated_at' => Yii::$app->mv->gt('Updated At', [], 0),
            'created_by' => Yii::$app->mv->gt('Created By', [], 0),
            'user_id' => Yii::$app->mv->gt('User ID', [], 0),
            'updated_by' => Yii::$app->mv->gt('Updated By', [], 0),
            'amount' => Yii::$app->mv->gt('Amount', [], 0),
            'transaction_id' => Yii::$app->mv->gt('Transaction ID', [], 0),
        ];
    }

}
