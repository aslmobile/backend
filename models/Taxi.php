<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "taxi".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $trip_id
 * @property float $tariff
 * @property integer $status
 * @property string $address
 * @property integer $checkpoint
 * @property integer $created_at
 * @property integer $updated_at
 */
class Taxi extends \yii\db\ActiveRecord
{
    const
        STATUS_NEW = 1,
        STATUS_PROCESSING = 2,
        STATUS_DONE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'taxi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'checkpoint', 'status', 'created_at', 'updated_at', 'trip_id'], 'integer'],
            ['tariff', 'double'],
            [['address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'user_id' => Yii::$app->mv->gt('Пассажир', [], 0),
            'trip_id' => Yii::$app->mv->gt('Поездка', [], 0),
            'tariff' => Yii::$app->mv->gt('Тариф', [], 0),
            'status' => Yii::$app->mv->gt('Статус', [], 0),
            'address' => Yii::$app->mv->gt('Адрес', [], 0),
            'checkpoint' => Yii::$app->mv->gt('Куда', [], 0),
            'created_at' => Yii::$app->mv->gt('Создан', [], 0),
            'updated_at' => Yii::$app->mv->gt('Изменен', [], 0),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_NEW => Yii::t('app', "Новый"),
            self::STATUS_PROCESSING => Yii::t('app', "В процессе"),
            self::STATUS_DONE => Yii::t('app', "Завершен")
        ];
    }

    public function getUser()
    {
        return \app\modules\admin\models\User::findOne($this->user_id);
    }

    public function getCheckPoint()
    {
        return \app\modules\admin\models\Checkpoint::findOne($this->checkpoint);
    }
}
