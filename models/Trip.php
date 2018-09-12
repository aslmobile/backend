<?php namespace app\models;

use app\components\Socket\SocketPusher;
use app\modules\api\models\Users;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "trip".
 *
 * @property int $id
 * @property int $status
 * @property integer $user_id
 * @property integer $driver_id
 * @property float $amount
 * @property float $tariff
 * @property integer $cancel_reason
 * @property string $passenger_description
 * @property string $driver_description
 * @property string $currency
 * @property int $payment_type
 * @property int $payment_status
 * @property float $passenger_rating
 * @property float $driver_rating
 * @property integer $startpoint_id
 * @property integer $endpoint_id
 * @property integer $route_id
 * @property int $seats
 * @property string $driver_comment
 * @property string $passenger_comment
 * @property integer $vehicle_id
 * @property integer $vehicle_type_id
 * @property string $luggage_unique_id
 * @property integer $line_id
 * @property integer $need_taxi
 * @property integer $taxi_status
 * @property integer $taxi_cancel_reason
 * @property string $taxi_address
 * @property integer $taxi_time
 * @property integer $scheduled
 * @property integer $schedule_id
 * @property integer $start_time
 * @property integer $finish_time
 * @property integer $position
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 *
 * @property \app\models\Checkpoint $startpoint
 * @property \app\models\User $user
 */
class Trip extends \yii\db\ActiveRecord
{
    const
        STATUS_CANCELLED = 0,
        STATUS_CREATED = 1,
        STATUS_WAITING = 2,
        STATUS_WAY = 3,
        STATUS_FINISHED = 4,
        STATUS_SCHEDULED = 5,
        STATUS_CANCELLED_DRIVER = 9;

    const
        TAXI_STATUS_PENDING = 0,
        TAXI_STATUS_REQUESTED = 1,
        TAXI_STATUS_ARRIVED = 2,
        TAXI_STATUS_WAY = 3,
        TAXI_STATUS_DELIVERED = 4;

    public static function tableName()
    {
        return 'trip';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    'driver_id',
                    'vehicle_id',
                    'line_id'
                ],
                'required'
            ],

            [
                [
                    'status',
                    'user_id',
                    'driver_id',
                    'cancel_reason',
                    'payment_type',
                    'payment_status',
                    'startpoint_id',
                    'endpoint_id',
                    'route_id',
                    'seats',
                    'vehicle_id',
                    'vehicle_type_id',
                    'line_id',
                    'need_taxi',
                    'taxi_status',
                    'taxi_cancel_reason',
                    'taxi_time',
                    'scheduled',
                    'schedule_id',
                    'start_time',
                    'finish_time'
                ],
                'integer'
            ],

            [
                [
                    'amount',
                    'tariff',
                    'passenger_rating',
                    'driver_rating',
                    'passenger_rating',
                    'driver_rating'
                ],
                'number'
            ],

            [
                [
                    'passenger_description',
                    'driver_description',
                    'currency',
                    'driver_comment',
                    'passenger_comment',
                    'luggage_unique_id',
                    'taxi_address',
                    'position'
                ],
                'string'
            ],
            ['position', 'default', 'value' => '0.0,0.0']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'user_id' => Yii::t('app', "Пассажир"),
            'driver_id' => Yii::t('app', "Водитель"),
            'vehicle_type_id' => Yii::t('app', "Тип автомобиля"),
            'startpoint_id' => Yii::t('app', "Остановка"),
            'status' => Yii::t('app', "Статус"),
            'created_at' => Yii::t('app', "Создана"),
            'updated_at' => Yii::t('app', "Обновлена"),
            'position' => Yii::t('app', "Позиция на карте"),
            'taxi_status' => Yii::t('app', "Статус заказа"),
            'taxi_cancel_reason' => Yii::t('app', "Причина отказа"),
            'taxi_address' => Yii::t('app', "Адрес подачи"),
            'taxi_time' => Yii::t('app', "На какое время"),
            'seats' => Yii::t('app', "Места"),
            'endpoint_id' => Yii::t('app', "Конечная"),
            'start_time' => Yii::t('app', "Время"),
            'line_id' => Yii::t('app', "Линия"),
            'route_id' => Yii::t('app', "Маршрут"),
            'amount' => Yii::t('app', "Сумма"),
            'tariff' => Yii::t('app', "Тариф"),
            'cancel_reason' => Yii::t('app', "Причина отмены"),
            'passenger_description' => Yii::t('app', "Комментарий пассажира"),
            'currency' => Yii::t('app', "Валюта"),
            'payment_type' => Yii::t('app', "Тип оплаты"),
            'passenger_rating' => Yii::t('app', "Рейтинг пассажира"),
            'driver_comment' => Yii::t('app', "Комментарий водителя"),
            'payment_status' => Yii::t('app', "Статус оплаты"),
            'luggage_unique_id' => Yii::t('app', "Уникальный ID багажа"),
            'passenger_comment' => Yii::t('app', "Комментарий пассажира"),
            'driver_rating' => Yii::t('app', "Рейтинг водителя"),
            'vehicle_id' => Yii::t('app', "Автомобиль"),
            'need_taxi' => Yii::t('app', "Заказ такси"),
            'scheduled' => Yii::t('app', "По расписанию"),
            'schedule_id' => Yii::t('app', "Номер расписания"),
            'finish_time' => Yii::t('app', "Время окончания"),
            'driver_description' => Yii::t('app', "Комментарий водителя"),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        /**
         * TODO: Начисление бесплатных КМ
         * TODO: Нотификации
         */

        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {

        $action = Yii::$app->controller->action->id;

        switch ($action) {

            case 'startpoint_id':

                $this->line_id = 0;
                $this->driver_id = 0;
                $this->vehicle_id = 0;

                $position = Checkpoint::findOne($this->startpoint_id);

                if (!empty($position)) {
                    $this->position = $position->latitude . ',' . $position->longitude;
                } else return false;

                break;

            case 'line_id':

                $line = Line::findOne($this->line_id);
                if (!empty($line)) {

                    $this->driver_id = $line->driver_id;
                    $this->vehicle_id = $line->vehicle_id;

                    $this->passenger_comment = '';
                    $this->driver_comment = '';
                    $this->driver_description = '';

                    $this->status = self::STATUS_WAITING;

                    if ($line->freeseats > $this->seats) {

                        $line->freeseats = $line->freeseats - $this->seats;

                    } else if ($line->freeseats == $this->seats) {

                        $line->freeseats = 0;
                        $line->status = Line::STATUS_WAITING;

                        if ($line->save()) {
                            $device = Devices::findOne(['user_id' => $this->driver_id]);
                            if ($device) {
                                $socket = new SocketPusher(['authkey' => $device->auth_token]);
                                $socket->push(base64_encode(json_encode([
                                    'action' => "acceptDriverTrip",
                                    'data' => ['message_id' => time()]
                                ])));
                            }

                        }

                    } else return false;

                } else return false;

                break;

            case 'status':

                $line = Line::findOne($this->line_id);

                $this->payment_type = \app\modules\api\models\Trip::PAYMENT_TYPE_CARD;
                $this->currency = 'T';

                if (empty($line)) break;


                switch ($this->status) {

                    case Trip::STATUS_CREATED:

                        break;

                    case Trip::STATUS_WAITING:

                        break;

                    case Trip::STATUS_WAY:

                        $this->payment_status = \app\modules\api\models\Trip::PAYMENT_STATUS_PAID;
                        $this->driver_comment = Yii::$app->mv->gt("Посадка подтверждена", [], false);
                        $this->start_time = time();

                        $device = Devices::findOne(['user_id' => $this->driver_id]);
                        if ($device) {
                            $socket = new SocketPusher(['authkey' => $device->auth_token]);
                            $socket->push(base64_encode(json_encode([
                                'action' => "acceptPassengerTrip",
                                'data' => ['message_id' => time()]
                            ])));
                        }

                        break;

                    case Trip::STATUS_CANCELLED:

                        $this->status = Trip::STATUS_CANCELLED;
                        $this->cancel_reason = Yii::$app->mv->gt('Поездка отменена', [], 0);
                        $this->line_id = 0;
                        $this->driver_id = 0;
                        $this->vehicle_id = 0;

                        $line->freeseats = $line->freeseats + $this->seats;
                        $line->save();

                        break;

                    case Trip::STATUS_CANCELLED_DRIVER:

                        $this->status = Trip::STATUS_CANCELLED;
                        $this->cancel_reason = Yii::$app->mv->gt('Поездка отменена водителем', [], 0);
                        $this->line_id = 0;
                        $this->driver_id = 0;
                        $this->vehicle_id = 0;

                        $line->freeseats = $line->freeseats + $this->seats;
                        $line->save();

                        break;

                    case Trip::STATUS_FINISHED:

                        break;
                }

                break;
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public static function getQueue($cache = false)
    {
        if ($cache) $queue = Yii::$app->cache->get('queue');
        else $queue = false;

        if (!$queue) {
            $_trips = self::find()->select(['id', 'user_id', 'vehicle_type_id', 'MAX(created_at) as created_at'])->where(['status' => self::STATUS_WAITING])->orderBy(['created_at' => SORT_DESC])->groupBy(['id', 'user_id', 'vehicle_type_id'])->all();
            /** @var \app\models\Trip $trip */

            $queue = [];
            foreach ($_trips as $trip) {
                $queue[$trip->vehicle_type_id]['vehicle_type_id'] = $trip->vehicle_type_id;
                $queue[$trip->vehicle_type_id]['queue'][] = [
                    'trip' => \app\modules\api\models\Trip::findOne($trip->id)->toArray(),
                    'user' => Users::findOne($trip->user_id)->toArray()
                ];
            }

            $queue = array_values($queue);
            Yii::$app->cache->set('queue', $queue, 300);
        }

        return $queue;
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_CANCELLED => Yii::t('app', "Отменена"),
            self::STATUS_CREATED => Yii::t('app', "Создана"),
            self::STATUS_WAITING => Yii::t('app', "В очереди"),
            self::STATUS_WAY => Yii::t('app', "В пути"),
            self::STATUS_FINISHED => Yii::t('app', "Завершена"),
            self::STATUS_CANCELLED_DRIVER => Yii::t('app', "Отменена водителем")
        ];
    }

    public static function getTaxiStatusList()
    {
        return [
            self::TAXI_STATUS_ARRIVED => Yii::t('app', "Подана"),
            self::TAXI_STATUS_DELIVERED => Yii::t('app', "Завершена"),
            self::TAXI_STATUS_PENDING => Yii::t('app', "В ожидании"),
            self::TAXI_STATUS_REQUESTED => Yii::t('app', "Запрошена"),
            self::TAXI_STATUS_WAY => Yii::t('app', "В пути")
        ];
    }

    public function getUser()
    {
        return User::findOne(['id' => $this->user_id]);
    }

    public function getStartpoint()
    {
        return Checkpoint::findOne($this->startpoint_id);
    }

    public function getRoutePoints()
    {
        return ArrayHelper::map(Checkpoint::findAll(['route' => $this->route_id, 'status' => Checkpoint::STATUS_ACTIVE]), 'id', 'title');
    }

    public static function getAllRoutePoints()
    {
        $route_ids = ArrayHelper::getColumn(Trip::find()->all(), 'route_id');
        if (!empty($route_ids)) {
            return ArrayHelper::map(Checkpoint::findAll(['route' => $route_ids, 'status' => Checkpoint::STATUS_ACTIVE]), 'id', 'title');
        }
        return [];
    }

    public function getLines()
    {
        return ArrayHelper::map(Line::find()->where([
            'line.route_id' => $this->route_id,
            'line.status' => [Line::STATUS_QUEUE, Line::STATUS_IN_PROGRESS, Line::STATUS_WAITING]
        ])->joinWith('routeR')->all(), 'id', 'driver.fullName');
    }

    public static function getAllLines()
    {
        $route_ids = ArrayHelper::getColumn(Trip::find()->all(), 'route_id');
        if (!empty($route_ids)) {
            return ArrayHelper::map(Line::find()->where([
                'line.route_id' => $route_ids,
                'line.status' => [Line::STATUS_QUEUE, Line::STATUS_IN_PROGRESS, Line::STATUS_WAITING]
            ])->joinWith('routeR')->all(), 'id', 'driver.fullName');
        }
        return [];
    }

    public function getEndpoint()
    {
        return Checkpoint::findOne($this->endpoint_id);
    }

    public function getLine()
    {
        return \app\modules\admin\models\Line::findOne($this->line_id);
    }

    public function getRoute()
    {
        return \app\modules\admin\models\Route::findOne($this->route_id);
    }

    public function getVehicleType()
    {
        return \app\modules\admin\models\VehicleType::findOne($this->vehicle_type_id);
    }

    public function getVehicle()
    {
        return \app\modules\api\models\Vehicles::findOne($this->vehicle_id);
    }

    public function getDriver()
    {
        return Users::findOne($this->driver_id);
    }

    public function getBaggages()
    {
        $baggage_list = [];
        $baggages = TripLuggage::find()->where([
            'AND',
            ['=', 'unique_id', $this->luggage_unique_id]
        ])->all();

        /** @var \app\models\TripLuggage $baggage */
        if ($baggages && count($baggages) > 0) foreach ($baggages as $baggage) {
            $baggage_list[] = [
                'id' => $baggage->id,
                'size' => $baggage->luggageType->title,
                'need_seat' => intval($baggage->need_place),
                'amount' => $baggage->amount
            ];
        }

        return $baggage_list;
    }

    public function getCalculatedAmount()
    {
        $amount = floatval($this->amount);
        $luggage_amount = 0.0;

        $luggages = TripLuggage::find(['unique_id' => $this->luggage_unique_id])->all();
        if ($luggages) foreach ($luggages as $luggage) {
            if (floatval($luggage->amount) > 0) $luggage_amount += floatval($luggage->amount);
        }

        if ($luggage_amount > 0) return round($amount, 2) . ' (+' . $luggage_amount . ')';

        return round($amount, 2);
    }

    public function getSummaryAmount()
    {
        $amount = floatval($this->amount);

        $luggages = TripLuggage::find(['unique_id' => $this->luggage_unique_id])->all();
        if ($luggages) foreach ($luggages as $luggage) {
            if (floatval($luggage->amount) > 0) $amount += floatval($luggage->amount);
        }

        return round($amount, 2);
    }

    public function getSummarySeats()
    {
        $seats = intval($this->seats);

        $luggages = TripLuggage::find(['unique_id' => $this->luggage_unique_id, 'need_place' => 1])->all();
        if ($luggages) foreach ($luggages as $luggage) {
            if (intval($luggage->seats) > 0) $seats += intval($luggage->seats);
        }

        return $seats;
    }

    public static function getVehicleTypeList()
    {
        $types = Yii::$app->cache->get('vehicle_types');
        if (!$types) {
            $types = \app\modules\admin\models\VehicleType::find()->orderBy(['title' => SORT_ASC])->all();
            Yii::$app->cache->set('vehicle_types', $types, 900);
        }

        $list = [];
        /** @var \app\modules\admin\models\VehicleType $type */
        if ($types && count($types) > 0) foreach ($types as $type) {
            $list[$type->id] = $type->title;
        }

        return $list;
    }
}
