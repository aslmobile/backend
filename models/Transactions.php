<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "transactions".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $user_id
 * @property integer $recipient_id
 * @property double $amount
 * @property integer $gateway
 * @property integer $cancel_reason
 * @property string $gateway_status
 * @property string $gateway_response
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $uip
 * @property string $currency
 * @property integer $route_id
 * @property integer $trip_id
 * @property integer $line_id
 * @property integer $type
 * @property string $request
 * @property string $response
 * @property int $payment_id
 * @property string $payment_link
 *
 * @property User $recipient
 * @property Route $route
 *
 */
class Transactions extends \yii\db\ActiveRecord
{

    //region CONSTANTA

    const
        TYPE_OUTCOME = 1,
        TYPE_INCOME = 2;

    const
        STATUS_REQUEST = 0,
        STATUS_PAID = 1,
        STATUS_CANCELLED = 2,
        STATUS_REJECTED = 3,
        STATUS_WAITING = 4;

    const

        GATEWAY_PAYBOX = 1,
        GATEWAY_CASH = 2,
        GATEWAY_KM = 3,
        GATEWAY_PAYBOX_CARD = 4,

        GATEWAY_COMMISSION = 5,
        GATEWAY_IN = 6,
        GATEWAY_OUT  = 7;

    //endregion

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transactions';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'created_at',
                'updated_at',
                'status',
                'user_id',
                'route_id',
                'line_id',
                'trip_id',
                'recipient_id',
                'gateway',
                'cancel_reason',
                'created_by',
                'updated_by',
                'type'
            ], 'integer'],
            [['amount'], 'number'],
            [['gateway_response', 'request', 'response'], 'string'],
            [['gateway_status', 'uip', 'currency'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'created_at' => Yii::$app->mv->gt('Создана', [], 0),
            'updated_at' => Yii::$app->mv->gt('Обновлена', [], 0),
            'status' => Yii::$app->mv->gt('Статус', [], 0),
            'user_id' => Yii::$app->mv->gt('Пользователь', [], 0),
            'recipient_id' => Yii::$app->mv->gt('Адресат', [], 0),
            'amount' => Yii::$app->mv->gt('Сумма', [], 0),
            'gateway' => Yii::$app->mv->gt('Сервис', [], 0),
            'cancel_reason' => Yii::$app->mv->gt('Причина отмены', [], 0),
            'gateway_status' => Yii::$app->mv->gt('Статус сервиса', [], 0),
            'gateway_response' => Yii::$app->mv->gt('Ответ сервиса', [], 0),
            'uip' => Yii::$app->mv->gt('IP пользователя', [], 0),
            'currency' => Yii::$app->mv->gt('Валюта', [], 0),
            'type' => Yii::$app->mv->gt('Тип транзакции', [], 0),
            'request' => Yii::$app->mv->gt('Запрос', [], 0),
            'response' => Yii::$app->mv->gt('Ответ', [], 0),
            'route_id' => Yii::$app->mv->gt('Маршрут', [], 0),
            'trip_id' => Yii::$app->mv->gt('Поездка', [], 0),
            'line_id' => Yii::$app->mv->gt('Линия', [], 0),
        ];
    }

    public static function getTypeList()
    {
        return [
            self::TYPE_INCOME => Yii::t('app', "Пополнение"),
            self::TYPE_OUTCOME => Yii::t('app', "Оплата")
        ];
    }

    public static function getTypeListArrows()
    {
        return [
            self::TYPE_INCOME => Yii::t('app', "<i class='fa fa-long-arrow-up text-success'></i>"),
            self::TYPE_OUTCOME => Yii::t('app', "<i class='fa fa-long-arrow-down text-danger'></i>")
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_REQUEST => Yii::t('app', "Запрос на оплату в обработке"),
            self::STATUS_PAID => Yii::t('app', "Оплачено"),
            self::STATUS_CANCELLED => Yii::t('app', "Отменен"),
            self::STATUS_REJECTED => Yii::t('app', "Заблокирован")
        ];
    }

    public static function getGatewayServices()
    {
        return [
            self::GATEWAY_PAYBOX => Yii::t('app', "PayBox"),
            self::GATEWAY_PAYBOX_CARD => Yii::t('app', "PayBox CARD"),
            self::GATEWAY_CASH => Yii::t('app', "Наличные"),
            self::GATEWAY_KM => Yii::t('app', "Бесплатные КМ"),
            self::GATEWAY_COMMISSION => Yii::t('app', "Комиссия")
        ];
    }

    public static function getPaymentMethods()
    {
        return [
            [
                'id' => self::GATEWAY_PAYBOX,
                'value' => Yii::t('app', "PayBox")
            ],
            [
                'id' => self::GATEWAY_PAYBOX_CARD,
                'value' => Yii::t('app', "PayBox CARD")
            ],
            [
                'id' => self::GATEWAY_CASH,
                'value' => Yii::t('app', "Наличные")
            ],
            [
                'id' => self::GATEWAY_KM,
                'value' => Yii::t('app', "Бесплатные километры")
            ]
        ];
    }

    public static function getInMethods()
    {
        return [
            [
                'id' => self::GATEWAY_PAYBOX,
                'value' => Yii::t('app', "PayBox")
            ],
        ];
    }

    public static function getOutMethods()
    {
        return [
            [
                'id' => self::GATEWAY_PAYBOX_CARD,
                'value' => Yii::t('app', "PayBox CARD")
            ],
        ];
    }

    public function getUser()
    {
        return User::findOne($this->user_id);
    }

    public function getRecipient()
    {
        return User::findOne($this->recipient_id);
    }

    public function getRoute()
    {
        return Route::findOne($this->route_id);
    }

    public function getLine()
    {
        return Line::findOne($this->line_id);
    }

    public function getTrip()
    {
        return Trip::findOne($this->route_id);
    }
}
