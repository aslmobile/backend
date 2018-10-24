<?php

namespace app\models;

use app\components\NotNullBehavior;
use app\components\UserBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

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

    const STATUS_NEW = 0;
    const STATUS_PAYED = 1;
    const STATUS_REJECTED = 2;

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

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            UserBehavior::class,
            NotNullBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'status' => Yii::$app->mv->gt('Статус', [], 0),
            'created_at' => Yii::$app->mv->gt('Создано', [], 0),
            'updated_at' => Yii::$app->mv->gt('Обновлено', [], 0),
            'created_by' => Yii::$app->mv->gt('Создал', [], 0),
            'user_id' => Yii::$app->mv->gt('Водитель', [], 0),
            'updated_by' => Yii::$app->mv->gt('Обновил', [], 0),
            'amount' => Yii::$app->mv->gt('Сумма', [], 0),
            'transaction_id' => Yii::$app->mv->gt('Транзакция', [], 0),
        ];
    }

    public static function statusLabels()
    {
        return [
            self::STATUS_NEW => Yii::t('app', 'Новая'),
            self::STATUS_PAYED => Yii::t('app', 'Оплачена'),
            self::STATUS_REJECTED => Yii::t('app', 'Отменена'),
        ];
    }

    public function getStatusLabel()
    {
        return isset(self::statusLabels()[$this->status]) ? self::statusLabels()[$this->status] : '';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getTransaction()
    {
        return $this->hasOne(Transactions::class, ['id' => 'transaction_id']);
    }

    public function getUpdated()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getCreated()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

}
