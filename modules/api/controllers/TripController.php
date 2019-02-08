<?php namespace app\modules\api\controllers;

use app\components\ArrayQuery\ArrayQuery;
use app\components\Socket\SocketPusher;
use app\models\Answers;
use app\models\Checkpoint;
use app\models\Dispatch;
use app\models\Line;
use app\models\LuggageType;
use app\models\Notifications;
use app\models\Queue;
use app\models\Route;
use app\models\Taxi;
use app\models\Transactions;
use app\models\TripLuggage;
use app\models\User;
use app\modules\admin\models\Km;
use app\modules\api\models\Devices;
use app\modules\api\models\RestFul;
use app\modules\api\models\Trip;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/** @property \app\modules\api\Module $module */
class TripController extends BaseController
{
    public $modelClass = 'app\modules\api\models\RestFul';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'calculate-driver-tariff',
                            'calculate-passenger-tariff',
                            'accept-seat',
                            'accept-passenger',
                            'passenger-comments',
                            'driver-comments',
                            'trips',
                            'passengers',
                            'checkpoint-arrived',
                            'luggage-type',
                            'taxi',
                            'penalty',
                            'queue',
                            'passengers-route',
                            'passenger-trips',
                            'decline-passenger',
                            'cancel-trip-queue',
                            'rate-passenger',
                            'comment-passenger',
                            'rate-driver',
                            'return',
                            'get-trip',
                            'update-trip',
                            'get-km',
                            'start-editing', 'stop-editing'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'penalty' => ['GET'],
                    'calculate-driver-tariff' => ['POST'],
                    'calculate-passenger-tariff' => ['POST'],
                    'passengers' => ['GET'],
                    'accept-seat' => ['POST'],
                    'accept-passenger' => ['POST'],
                    'checkpoint-arrived' => ['POST'],
                    'taxi' => ['POST'],
                    'luggage-type' => ['GET'],
                    'queue' => ['PUT'],
                    'passengers-route' => ['GET'],
                    'arrive-endpoint' => ['POST'],
                    'passenger-trips' => ['GET'],
                    'decline-passenger' => ['POST'],
                    'cancel-trip-queue' => ['POST'],
                    'rate-passenger' => ['POST'],
                    'comment-passenger' => ['POST'],
                    'rate-driver' => ['POST'],
                    'return' => ['POST'],
                    'get-trip' => ['GET'],
                    'update-trip' => ['POST'],
                    'get-km' => ['GET'],
                    'start-editing' => ['GET'],
                    'stop-editing' => ['GET']
                ]
            ]
        ];
    }

    public function beforeAction($event)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        return parent::beforeAction($event);
    }

    public function actionGetKm()
    {

        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $km_settings = Km::findOne(1);
        $km_desc = $km_settings->description;

        $this->module->data['km'] = $user->km;
        $this->module->data['min'] = Yii::$app->params['distance'];
        $this->module->data['description'] = $km_desc;
        $this->module->data['settings'] = [
            'accumulation' => $km_settings->settings_accumulation,
            'waste' => $km_settings->settings_waste,
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();

    }

    public function actionPenalty()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $dispatch = Dispatch::findOne(1);
        if (!$dispatch) $dispatch = (object)['phone' => 70123456789];
        $penalty = Trip::findOne(['user_id' => $user->id, 'penalty' => 1]);
        if (!$penalty) $penalty = (object)['id' => -1, 'amount' => 0]; else {
            $penalty = (object)[
                'id' => $penalty->id,
                'amount' => $penalty->amount / 2,
                'cancel_reason' => $penalty->driver_description,
                'phone' => $dispatch->phone,
            ];
        }

        $this->module->data['penalty'] = $penalty;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionQueue()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $state = $user->toArray();
        if ($state['queue'] || $state['online'])
            $this->module->setError(422, '_trip', Yii::$app->mv->gt("Вы уже на маршруте!", [], false));

        $this->prepareBody();
        $this->validateBodyParams(['checkpoint', 'route', 'time', 'seats', 'payment_type', 'vehicle_type_id']);

        $penalty = Trip::findOne(['user_id' => $user->id, 'penalty' => 1]);
        $penalty_amount = 0;

        if ($penalty) {
            $penalty_amount = $penalty->amount / 2;
            if (!isset($this->body->penalty) || $this->body->penalty != $penalty_amount || $this->body->payment_type != Trip::PAYMENT_TYPE_CASH)
                $this->module->setError(422, '_penalty', Yii::$app->mv->gt("У вас не оплачен штраф", [], false));
        }

        $route = Route::findOne(['status' => Route::STATUS_ACTIVE, 'id' => $this->body->route]);
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        if ($this->body->payment_type == Trip::PAYMENT_TYPE_KM) {

            if (isset($this->body->schedule) && !empty($this->body->schedule)) $this->module->setError(422,
                '_km', Yii::$app->mv->gt("Вы не можете оплатить зпланированную поездку бесплатными километрами", [], false));

            $query = new ArrayQuery();

            $km_settings = \app\models\Km::findOne(1);
            $km_waste = $km_settings->settings_waste;
            $day = date('N');
            $time = intval(str_replace(':', '', date('H:i')));
            $query->from($km_waste);

            $waste_exist = $query->where(['route' => strval($route->id)])->one();

            if ($waste_exist) {
                $waste = $query->where(['CALLBACK', function ($data) use ($day) {
                    return in_array($day, $data['days']);
                }])->andWhere(['route' => strval($route->id)])
                    ->andWhere(['<=', 'from', $time])->andWhere(['>=', 'to', $time])
                    ->one();
                if (!$waste || $user->km < Yii::$app->params['distance']) $this->module->setError(422,
                    '_km', Yii::$app->mv->gt("Вы не можете оплатить данную поездку бесплатными километрами", [], false));
            }

        }

        $chp_id = $this->body->checkpoint;
        $checkpoint = Checkpoint::findOne(['type' => Checkpoint::TYPE_STOP, 'status' => Checkpoint::STATUS_ACTIVE, 'id' => $chp_id]);
        if (!$checkpoint) $this->module->setError(422, '_checkpoint', Yii::$app->mv->gt("Не найден", [], false));

        $endpoint = Checkpoint::findOne(['type' => Checkpoint::TYPE_END, 'status' => Checkpoint::STATUS_ACTIVE, 'route' => $route->id]);
        if (!$endpoint) $this->module->setError(422, '_endpoint', Yii::$app->mv->gt("Не найден", [], false));

        $seats = $this->body->seats;
        $_luggages = [];
        $luggages = $this->body->luggage;
        if (is_array($luggages) && count($luggages) > 0) foreach ($luggages as $luggage) {
            $luggage = LuggageType::findOne($luggage);
            if (!$luggage) $this->module->setError(422, '_luggage', Yii::$app->mv->gt("Не найден", [], false));
            $_luggages[] = $luggage->toArray();
        }

        $luggage_unique = false;
        if ($_luggages && count($_luggages) > 0) {
            foreach ($_luggages as $luggage) $luggage_unique .= $luggage['id'] . '+';
            $luggage_unique .= $user->id . '+' . $route->id;
            $luggage_unique = hash('sha256', md5($luggage_unique) . time());
        }

        $taxi = false;
        if (isset($this->body->taxi) && !empty($this->body->taxi)) {
            $taxi = new Taxi([
                'checkpoint' => $checkpoint->id,
                'user_id' => $user->id,
                'address' => $this->body->taxi,
                'status' => Taxi::STATUS_NEW
            ]);
        }

        $trip = new Trip();
        $trip->status = Trip::STATUS_CREATED;
        $trip->user_id = $user->id;
        $trip->tariff = $this->calculateTariff($route->id)['tariff'];
        $trip->currency = "₸";
        $trip->payment_type = $this->body->payment_type;
        $trip->startpoint_id = $checkpoint->id;
        $trip->route_id = $route->id;
        $trip->seats = intval($seats);
        $trip->amount = $trip->seats * $trip->tariff;
        $trip->endpoint_id = $endpoint->id;
        $trip->payment_status = Trip::PAYMENT_STATUS_WAITING;
        $trip->passenger_description = $this->body->comment;
        $trip->need_taxi = $this->body->taxi ? 1 : 0;
        $trip->queue_time = $this->body->time == -1 ? time() : $this->body->time;
        $trip->start_time = $this->body->time == -1 ? time() + 1800 : $this->body->time;

        if ($taxi) {
            $trip->taxi_status = $taxi->status;
            $trip->taxi_address = $taxi->address;
            $trip->taxi_time = $this->body->time == -1 ? time() + 900 : $this->body->time;
        }

        if ($luggage_unique) {

            $trip->luggage_unique_id = (string)$luggage_unique;

            /** @var \app\models\TripLuggage $luggage */
            foreach ($_luggages as $luggage) {

                if ($luggage['need_place']) {
                    $tariff = (object)$this->calculateTariff($route->id);
                    $amount = (int)intval($luggage['seats']) * (float)floatval($tariff->tariff);
                } else $amount = (float)floatval(0.0);

                $_trip_luggage = new TripLuggage();
                $_trip_luggage->unique_id = strval($luggage_unique);
                $_trip_luggage->amount = floatval($amount);
                $_trip_luggage->status = 0;
                $_trip_luggage->need_place = intval($luggage['need_place']);
                $_trip_luggage->seats = intval($luggage['seats']);
                $_trip_luggage->currency = strval("₸");
                $_trip_luggage->luggage_type = intval($luggage['id']);

                $_trip_luggage->save();

                $trip->seats += $_trip_luggage->seats;
                $trip->amount += $_trip_luggage->amount;
            }

        }

        if ($trip->payment_type == Trip::PAYMENT_TYPE_CASH) $trip->amount += $penalty_amount;
        $trip->driver_id = 0;
        $trip->vehicle_id = 0;
        $trip->vehicle_type_id = $this->body->vehicle_type_id;
        $trip->line_id = 0;

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422,
                        'trip.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не удалось сохранить поездку", [], false));
        }

        if ($taxi) {
            $taxi->trip_id = $trip->id;
            $taxi->save();
        }

        $future_text = Yii::t('app', 'В');
        $future_text .= ' ' . date('H:i', $this->body->time) . ' ';
        $future_text .= Yii::t('app', 'вы автоматически станете в очередь на поездку');
        $future_text .= $route->title;

        if (isset($this->body->schedule) && !empty($this->body->schedule)) {
            $schedule = json_encode($this->body->schedule);
            Trip::cloneTrip($trip, Trip::STATUS_SCHEDULED, false, $schedule);
            Notifications::create(
                Notifications::NTP_TRIP_SCHEDULED,
                [$trip->user_id],
                $future_text,
                $trip->id,
                Notifications::STATUS_SCHEDULED,
                $this->body->time
            );
        } else if (($this->body->time - time()) >= (3600 + 900)) {
            Notifications::create(
                Notifications::NTP_TRIP_SCHEDULED,
                [$trip->user_id],
                $future_text,
                $trip->id,
                Notifications::STATUS_SCHEDULED,
                $this->body->time
            );
        }

        Queue::processingQueue();

        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionGetTrip($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $trip = Trip::findOne($id);

        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $watchdog = RestFul::find()->where([
            'AND',
            ['=', 'type', RestFul::TYPE_PASSENGER_ACCEPT_SEAT],
            ['=', 'user_id', $trip->user_id],
            ['=', 'message', json_encode(['status' => 'request'])],
            ['>', 'created_at', time() - 300],
        ])->one();

        $this->module->data['trip'] = $trip->toArray();
        $this->module->data['acceptSeat'] = !empty($watchdog) ? [
            'seat_from' => $watchdog->created_at,
            'seat_time' => 300
        ] : (object)[];

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionReturn($id)
    {

        /** @var \app\modules\api\models\Trip $trip */
        $trip = Trip::findOne(['id' => $id, 'status' => [Trip::STATUS_CREATED, Trip::STATUS_WAITING]]);
        if (!$trip)
            $this->module->setError(422, '_trip', Yii::$app->mv->gt("Вы уже не можете вернутся в очередь на данной поездке", [], false));

        /** @var \app\modules\api\models\Line $line */
        $line = Line::findOne(['id' => $trip->line_id, 'status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]]);
        if ($trip->line_id != 0 && !$line)
            $this->module->setError(422, '_line', Yii::$app->mv->gt("Вы уже не можете вернутся в очередь с данной поездки", [], false));

        $not = !empty($trip->not) ? json_decode($trip->not) : [];
        $not += !empty($line) ? [$line->id] : [];

        $trip->status = Trip::STATUS_CREATED;
        $trip->driver_id = 0;
        $trip->vehicle_id = 0;
        $trip->line_id = 0;
        $trip->not = json_encode($not);

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message, [], false),
                        true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не удалось сохранить поездку", [], false));
        }

        RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
            'AND',
            ['user_id' => $trip->user_id],
            ['type' => [RestFul::TYPE_PASSENGER_ACCEPT, RestFul::TYPE_PASSENGER_ACCEPT_SEAT]]
        ]);

        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $trip->user_id]);
        if (!$device) $this->module->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
        $socket = new SocketPusher(['authkey' => $device->auth_token]);

        if (!empty($line)) {
            $socket->push(base64_encode(json_encode([
                'action' => "disbandedTrip",
                'notifications' => [],
                'data' => ['message_id' => time(), 'trip_id' => $trip->id, 'line' => $line->toArray()]
            ])));
        }

        $socket->push(base64_encode(json_encode([
            'action' => "driverQueue",
            'notifications' => [],
            'data' => ['message_id' => time()]
        ])));

        Queue::processingQueue();

        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();

    }

    public function actionStartEditing($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $trip = Trip::findOne(['id' => $id, 'line_id' => 0, 'status' => Trip::STATUS_CREATED]);
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Редактирование невозможно", [], false));

        $trip->status = Trip::STATUS_EDITING;
        $trip->save();

        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionStopEditing($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $trip = Trip::findOne(['id' => $id, 'status' => Trip::STATUS_EDITING]);
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->status = Trip::STATUS_CREATED;
        $trip->save();

        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUpdateTrip($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;
        $this->prepareBody();

        $penalty = Trip::findOne(['user_id' => $user->id, 'penalty' => 1]);
        $penalty_amount = 0;

        if ($penalty) {
            $penalty_amount = $penalty->amount / 2;
            if (!isset($this->body->penalty) || $this->body->penalty != $penalty_amount)
                $this->module->setError(422, '_penalty', Yii::$app->mv->gt("У вас не оплачен штраф", [], false));
        }

        $trip = Trip::findOne(['id' => $id, 'status' => [Trip::STATUS_SCHEDULED, Trip::STATUS_EDITING]]);
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Поездку нельзя изменить", [], false));

        $trip->line_id = 0;
        $trip->vehicle_id = 0;

        $luggage_seats = 0;
        $luggage_amount = 0;

        if (isset($this->body->route) && !empty($this->body->route)) {
            $route = Route::findOne(['status' => Route::STATUS_ACTIVE, 'id' => $this->body->route]);
        } else $route = Route::findOne($trip->route_id);
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        if (isset($this->body->checkpoint) && !empty($this->body->checkpoint)) {
            $chp_id = $this->body->checkpoint;
            $checkpoint = Checkpoint::findOne(['type' => Checkpoint::TYPE_STOP, 'status' => Checkpoint::STATUS_ACTIVE, 'id' => $chp_id]);
        } else $checkpoint = Checkpoint::findOne($trip->startpoint_id);
        if (!$checkpoint) $this->module->setError(422, '_checkpoint', Yii::$app->mv->gt("Не найден", [], false));

        $endpoint = Checkpoint::findOne(['type' => Checkpoint::TYPE_END, 'status' => Checkpoint::STATUS_ACTIVE, 'route' => $route->id]);
        if (!$endpoint) $this->module->setError(422, '_endpoint', Yii::$app->mv->gt("Не найден", [], false));

        if (isset($this->body->seats) && !empty($this->body->seats))
            $seats = $this->body->seats; else $seats = $trip->seats;
        if (isset($this->body->payment_type) && !empty($this->body->payment_type))
            $payment_type = $this->body->payment_type; else $payment_type = $trip->payment_type;
        if (isset($this->body->comment) && !empty($this->body->comment))
            $comment = $this->body->comment; else $comment = $trip->passenger_description;
        if (isset($this->body->time) && !empty($this->body->time)) {
            $time = $this->body->time == -1 ? time() + 1800 : $this->body->time;
            $queue_time = $this->body->time == -1 ? time() : $this->body->time;
        } else {
            $time = $trip->start_time;
            $queue_time = $trip->queue_time;
        }

        if (isset($this->body->vehicle_type_id) && !empty($this->body->vehicle_type_id))
            $vehicle_type_id = $this->body->vehicle_type_id; else $vehicle_type_id = $trip->vehicle_type_id;

        $luggage_unique = false;

        /** @var TripLuggage $luggage */
        if (!empty($trip->luggages)) foreach ($trip->luggages as $luggage) {
            $luggage_amount += $luggage->amount;
            $luggage_seats += $luggage->seats;
        }

        if (isset($this->body->luggage) && is_array($this->body->luggage)) {

            TripLuggage::deleteAll(['unique_id' => $trip->luggage_unique_id]);
            $luggage_unique = true;

            $_luggages = [];
            $luggages = $this->body->luggage;
            if (is_array($luggages) && count($luggages) > 0) foreach ($luggages as $luggage) {
                $luggage = LuggageType::findOne($luggage);
                if (!$luggage) $this->module->setError(422, '_luggage', Yii::$app->mv->gt("Не найден", [], false));
                $_luggages[] = $luggage->toArray();
            }
            if ($_luggages && count($_luggages) > 0) {
                foreach ($_luggages as $luggage) $luggage_unique .= $luggage['id'] . '+';
                $luggage_unique .= $user->id . '+' . $route->id;
                $luggage_unique = hash('sha256', md5($luggage_unique) . time());
            }

        }

        $taxi = false;
        Taxi::deleteAll(['trip_id' => $trip->id]);
        $trip->taxi_status = 0;
        $trip->taxi_address = '';
        $trip->taxi_time = 0;
        if (isset($this->body->taxi) && !empty($this->body->taxi)) {
            $taxi = new Taxi([
                'checkpoint' => $checkpoint->id,
                'user_id' => $user->id,
                'address' => $this->body->taxi,
                'status' => Taxi::STATUS_NEW
            ]);
        }

        $trip->tariff = $this->calculateTariff($route->id)['tariff'];
        $trip->currency = "₸";
        $trip->startpoint_id = $checkpoint->id;
        $trip->route_id = $route->id;
        $trip->endpoint_id = $endpoint->id;

        $trip->seats = $seats - $luggage_seats;
        $trip->amount = ($trip->seats * $trip->tariff) - $luggage_amount;

        $trip->payment_type = $payment_type;
        $trip->passenger_description = $comment;
        $trip->need_taxi = $taxi ? 1 : 0;
        $trip->queue_time = $queue_time;
        $trip->start_time = $time;
        $trip->vehicle_type_id = $vehicle_type_id;

        if ($taxi) {
            $trip->taxi_status = $taxi->status;
            $trip->taxi_address = $taxi->address;
            $trip->taxi_time = $time - 900;
        }

        if ($luggage_unique) {

            $trip->luggage_unique_id = (string)$luggage_unique;

            /** @var \app\models\TripLuggage $luggage */
            if (isset($_luggages)) foreach ($_luggages as $luggage) {

                if ($luggage['need_place']) {
                    $tariff = (object)$this->calculateTariff($route->id);
                    $amount = (int)intval($luggage['seats']) * (float)floatval($tariff->tariff);
                } else $amount = (float)floatval(0.0);

                $_trip_luggage = new TripLuggage();
                $_trip_luggage->unique_id = (string)$luggage_unique;
                $_trip_luggage->amount = (float)floatval($amount);
                $_trip_luggage->status = (int)0;
                $_trip_luggage->need_place = (int)intval($luggage['need_place']);
                $_trip_luggage->seats = (int)intval($luggage['seats']);
                $_trip_luggage->currency = (string)"₸";
                $_trip_luggage->luggage_type = (int)intval($luggage['id']);

                $_trip_luggage->save();

                $trip->seats += $_trip_luggage->seats;
                $trip->amount += $_trip_luggage->amount;
            }

        } else {

            $trip->seats += $luggage_seats;
            $trip->amount += $luggage_amount;

        }

        if ($trip->payment_type == Trip::PAYMENT_TYPE_CASH) $trip->amount += $penalty_amount;

        $future_text = Yii::t('app', 'В');
        $future_text .= ' ' . date('H:i', $this->body->time) . ' ';
        $future_text .= Yii::t('app', 'вы автоматически станете в очередь на поездку');
        $future_text .= $route->title;


        if (isset($this->body->schedule) && !empty($this->body->schedule)) {
            $schedule = json_encode($this->body->schedule);
            if ($trip->status == Trip::STATUS_SCHEDULED) {
                $trip->schedule = $schedule;
                Notifications::updateAll(['time' => $this->body->time, 'status' => Notifications::STATUS_NEW], [
                    'type' => Notifications::NTP_TRIP_SCHEDULED,
                    'user_id' => $trip->user_id,
                    'initiator_id' => $trip->id,
                ]);
            } else {
                Trip::cloneTrip($trip, Trip::STATUS_SCHEDULED, false, $schedule);
                Notifications::create(
                    Notifications::NTP_TRIP_SCHEDULED,
                    [$trip->user_id],
                    $future_text,
                    $trip->id,
                    Notifications::STATUS_SCHEDULED,
                    $this->body->time
                );
            }
        } else if (($this->body->time - time()) >= (3600 + 900)) {

            Notifications::updateAll(['status' => Notifications::STATUS_DELIVERED], [
                'type' => Notifications::NTP_TRIP_SCHEDULED,
                'user_id' => $trip->user_id,
                'initiator_id' => $trip->id,
            ]);

            Notifications::create(
                Notifications::NTP_TRIP_SCHEDULED,
                [$trip->user_id],
                $future_text,
                $trip->id,
                Notifications::STATUS_SCHEDULED,
                $this->body->time
            );
        }

        if ($trip->status == Trip::STATUS_EDITING) $trip->status = Trip::STATUS_CREATED;

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422,
                        'trip.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не удалось сохранить поездку", [], false));
        }

        if ($taxi) {
            $taxi->trip_id = $trip->id;
            $taxi->save();
        }

        Queue::processingQueue();

        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();

    }

    public function actionCancelTripQueue()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => [Trip::STATUS_CREATED, Trip::STATUS_WAITING]])
            ->orderBy(['created_at' => SORT_DESC])->one();

        if (!$trip)
            $this->module->setError(422, '_trip', Yii::$app->mv->gt("Вы уже не можете отменить очередь на данной поездке", [], false));

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne(['id' => $trip->line_id]);

        if (!isset ($this->body->cancel_reason)) $this->body->cancel_reason = 0;
        $trip->status = Trip::STATUS_CANCELLED;
        $trip->driver_id = 0;
        $trip->vehicle_id = 0;
        $trip->line_id = 0;
        $trip->cancel_reason = isset($this->body->cancel_reason) ? $this->body->cancel_reason : 0;
        $trip->passenger_comment = isset($this->body->passenger_comment) ? $this->body->passenger_comment : '';
        $trip->save();

        RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
            'AND',
            ['user_id' => $trip->user_id],
            ['type' => [RestFul::TYPE_PASSENGER_ACCEPT, RestFul::TYPE_PASSENGER_ACCEPT_SEAT]]
        ]);

        if (!empty($line)) {

            if ($line->status == Line::STATUS_IN_PROGRESS)
                $this->module->setError(422, '_trip', Yii::$app->mv->gt("Вы уже не можете отменить очередь на данной поездке", [], false));

            /** @var \app\models\Devices $device */
            $device = Devices::findOne(['user_id' => $trip->user_id]);
            if (!$device) $this->module->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
            $socket = new SocketPusher(['authkey' => $device->auth_token]);
            $socket->push(base64_encode(json_encode([
                'action' => "disbandedTrip",
                'notifications' => [],
                'data' => ['message_id' => time(), 'trip_id' => $trip->id, 'line' => $line->toArray()]
            ])));

        }

        Queue::processingQueue();

        $this->module->data['trip'] = $trip->toArray();

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    /**
     * Take a trip on a certain line by the passenger
     */
    public function actionAcceptPassenger()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['trip_id']);

        /** @var \app\modules\api\models\Trip $trip */
        $trip = Trip::findOne(['id' => $this->body->trip_id, 'status' => Trip::STATUS_CREATED]);
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Ваша поездка уже не в очереди", [], false));

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne(['id' => $trip->line_id, 'status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]]);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Водитель уже выехал или отменил поездку", [], false));

        $trip->driver_id = $line->driver_id;
        $trip->vehicle_id = $line->vehicle_id;
        $trip->status = Trip::STATUS_WAITING;

        $line->status = Line::STATUS_WAITING;

        $trip->save();
        $line->save();

        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $user->id]);
        if (!$device) $this->module->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
        $socket = new SocketPusher(['authkey' => $device->auth_token]);
        $socket->push(base64_encode(json_encode([
            'action' => "acceptPassengerTrip",
            'notifications' => [],
            'data' => ['message_id' => time(), 'addressed' => [$line->driver_id], 'trip' => $trip->toArray()]
        ])));

        $notifications = Notifications::create(Notifications::NTD_TRIP_ADD, [$line->driver_id], '', $user->id);
        if (is_array($notifications)) foreach ($notifications as $notification) Notifications::send($notification);

        RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
            'AND',
            ['user_id' => $trip->user_id],
            ['type' => [RestFul::TYPE_PASSENGER_ACCEPT, RestFul::TYPE_PASSENGER_ACCEPT_SEAT]]
        ]);

        Queue::processingQueue();

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    /**
     * Decline trip by passenger
     */
    public function actionDeclinePassenger()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['trip_id']);

        /** @var Trip $trip */
        $trip = Trip::find()
            ->where(['id' => $this->body->trip_id])
            ->where(['status' => [Trip::STATUS_WAY, Trip::STATUS_WAITING]])
            //->andWhere(['NOT', ['status' => [Trip::STATUS_CANCELLED, Trip::STATUS_CANCELLED_DRIVER]]])
            ->one();
        if (!$trip) $this->module->setError(422, '_line', Yii::$app->mv->gt("Поездку нельзя отменить", [], false));

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::find()
            ->where(['id' => $trip->line_id])
            ->where(['status' => Line::STATUS_IN_PROGRESS])
            //->andWhere(['NOT', ['status' => Line::STATUS_CANCELED]])
            ->one();
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Поездку нельзя отменить", [], false));

        $notifications = [];
        $addressed = [];
        $trip->cancel_reason = 0;

        switch ($user->type) {

            case User::TYPE_DRIVER:

                $addressed = [$trip->user_id];

                $reason = '';
                $trip->status = Trip::STATUS_CANCELLED_DRIVER;
                $trip->penalty = 1;
                $trip->driver_comment = isset($this->body->driver_comment) ?
                    $this->body->driver_comment :
                    \Yii::$app->mv->gt('Поездка отменена водителем', [], 0);

                if (isset($this->body->cancel_reason) && $this->body->cancel_reason) {
                    $trip->cancel_reason = $this->body->cancel_reason;
                    $answer = Answers::findOne(['type' => Answers::TYPE_CPR]);
                    if (!empty($answer)) {
                        $answer = json_decode($answer->answer);
                        $query = new ArrayQuery();
                        $query->from($answer);
                        $reason = $query->where(['id' => strval($this->body->cancel_reason)])->one();
                        if ($reason) $reason = $reason['answer'];
                    }

                }
                $trip->driver_description = $reason;

                $notifications = Notifications::create(
                    Notifications::NTP_TRIP_CANCEL, [$trip->user_id],
                    "Вам отказано в поездке. Причина - {$reason}",
                    $user->id
                );

                break;

            case User::TYPE_PASSENGER:

                $addressed = [$line->driver_id];

                if ($trip->status != Trip::STATUS_CREATED && $line->status == Line::STATUS_IN_PROGRESS) {
                    $trip->penalty = 1;
                    $trip->driver_description = \Yii::t('app', "Вы отменили поездку после выезда водителя.");
                }
                $trip->status = Trip::STATUS_CANCELLED;
                $trip->passenger_comment = isset($this->body->passenger_comment) ?
                    $this->body->passenger_comment : \Yii::$app->mv->gt('Поездка отменена', [], 0);

                break;
            default:
                $this->module->setError(422, '_user', Yii::$app->mv->gt("Не корректный пользователь", [], false));

        }

        $line->freeseats += $trip->seats;

        $trip->save();
        $line->save();
        $user->save();

        RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
            'AND',
            ['user_id' => $trip->user_id],
            ['type' => [RestFul::TYPE_PASSENGER_ACCEPT, RestFul::TYPE_PASSENGER_ACCEPT_SEAT]]
        ]);


        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $user->id]);
        if (!$device) $this->module->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
        $socket = new SocketPusher(['authkey' => $device->auth_token]);
        $socket->push(base64_encode(json_encode([
            'action' => "declinePassengerTrip",
            'notifications' => $notifications,
            'data' => ['message_id' => time(), 'addressed' => $addressed, 'trip' => $trip->toArray()]
        ])));

        Queue::processingQueue();

        $this->module->data['trip'] = $trip->toArray();
        $this->module->data['line'] = $line->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    /**
     * Accept arriving to checkpoint by driver
     *
     * @param $id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionCheckpointArrived($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['checkpoint_id']);

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne(['id' => $id, 'status' => Line::STATUS_IN_PROGRESS]);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Вы еще не выехали", [], false));

        /** @var Checkpoint $checkpoint */
        $checkpoint = Checkpoint::findOne(intval($this->body->checkpoint_id));
        if (!$checkpoint) $this->module->setError(422, 'checkpoint', Yii::$app->mv->gt("Точка не найдена", [], false));

        $data = ['line' => $line->toArray(), 'checkpoint' => $checkpoint->toArray()];
        $timer = true;

        if ($checkpoint->type == Checkpoint::TYPE_END) {

            $trips = Trip::find()->where(['line_id' => $line->id, 'status' => Trip::STATUS_WAY])->all();
            if (!$trips) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Пассажиры не найдены", [], false));

            $addressed = ArrayHelper::getColumn($trips, 'user_id');
            $timer = false;
            $notifications = Notifications::create(Notifications::NTP_TRIP_FINISHED, $addressed);

            $_trips = [];
            $total = ['cash' => 0, 'card' => 0];

            $query = new ArrayQuery();

            /** @var \app\models\Trip $trip */
            foreach ($trips as $trip) {

                $trip->status = Trip::STATUS_FINISHED;
                $trip->finish_time = time();

                if (
                    $trip->payment_type == Trip::PAYMENT_TYPE_CASH
                    &&
                    $penalty = Trip::findOne(['user_id' => $trip->user_id, 'penalty' => 1])
                ) {
                    if ($transaction = Transactions::findOne(['trip_id' => $penalty->id])) {
                        $transaction->status = Transactions::STATUS_PAID;
                        $penalty->penalty = 0;
                        $penalty->status = Trip::PAYMENT_STATUS_PAID;
                        $transaction->save();
                        $penalty->save();
                    }
                }

                switch ($trip->payment_type) {
                    case Trip::PAYMENT_TYPE_CASH:
                        $total['cash'] += $trip->amount;
                        break;
                    case Trip::PAYMENT_TYPE_CARD:
                        $total['card'] += $trip->amount;
                        break;
                }

                if (!$trip->validate() || !$trip->save()) {
                    if ($trip->hasErrors()) {
                        foreach ($trip->errors as $field => $error_message) {
                            $this->module->setError(422,
                                'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                        }
                        $this->module->sendResponse();
                    } else {
                        $this->module->setError(422, 'trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
                    }
                }
                $_trips[] = $trip->toArray();

                $km_settings = \app\models\Km::findOne(1);
                $km_accumulation = $km_settings->settings_accumulation;
                $day = date('N');
                $time = intval(str_replace(':', '', date('H:i')));
                $query->from($km_accumulation);

                $accumulation = $query->where(['CALLBACK', function ($data) use ($day) {
                    return in_array($day, $data['days']);
                }])->andWhere(['route' => strval($trip->route_id)])
                    ->andWhere(['<=', 'from', $time])->andWhere(['>=', 'to', $time])
                    ->one();

                if ($accumulation) {
                    $rate = doubleval($accumulation['rate']);
                    if ($rate && !empty($line->path)) {
                        $path = json_decode($line->path);
                        $distance = isset($path->distance) ? round($path->distance / 1000) : 0;
                        $trip->user->km += $distance * $rate;
                        $trip->user->update();
                    }
                }

            }

            $line->endtime = time();
            $line->status = Line::STATUS_FINISHED;
            $line->save();

            $commission = ($total['cash'] + $total['card']) / 10;

            $transaction = new Transactions();
            $transaction->user_id = $user->id;
            $transaction->recipient_id = 0;
            $transaction->status = Transactions::STATUS_PAID;
            $transaction->amount = $commission;
            $transaction->gateway = Transactions::GATEWAY_COMMISSION;
            $transaction->type = Transactions::TYPE_OUTCOME;
            $transaction->uip = Yii::$app->request->userIP;
            $transaction->currency = $trip->currency;
            $transaction->route_id = $line->route_id;
            $transaction->line_id = $line->id;
            $transaction->trip_id = 0;

            $user->balance -= $commission;

            $transaction->save();

            $data += ['trips' => $_trips, 'total' => $total];

        } else {

            $trips = Trip::findAll(['line_id' => $line->id, 'status' => Trip::STATUS_WAITING, 'startpoint_id' => $checkpoint->id]);
            $time = time();

            /** @var \app\models\Trip $trip */
            foreach ($trips as $trip) {
                $inAcceptSeat = new RestFul([
                    'type' => RestFul::TYPE_PASSENGER_ACCEPT_SEAT,
                    'user_id' => $trip->user_id,
                    'uip' => '0.0.0.0',
                    'message' => json_encode(['status' => 'request']),
                    'created_at' => $time,
                ]);
                $inAcceptSeat->save();
            }

            /** @var \app\models\Trip $trip */
            $addressed = ArrayHelper::getColumn($trips, 'user_id');
            $notifications = Notifications::create(Notifications::NTP_TRIP_ARRIVED, $addressed);
        }

        $user->save();

        if (is_array($notifications)) foreach ($notifications as $notification) Notifications::send($notification);

        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $user->id]);
        if (!$device) $this->module->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
        $socket = new SocketPusher(['authkey' => $device->auth_token]);
        $socket->push(base64_encode(json_encode([
            'action' => "checkpointArrived",
            'notifications' => [],
            'data' => [
                'message_id' => time(),
                'line' => $line->toArray(), 'checkpoint' => $checkpoint->toArray(),
                'addressed' => $addressed, 'timer' => $timer
            ]
        ])));

        $this->module->data = $data;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    /**
     * Accept landing in the vehicle by both of user types
     * @param $id
     */
    public function actionAcceptSeat($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['trip_id']);

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()->where(['id' => $this->body->trip_id, 'status' => Trip::STATUS_WAITING, 'line_id' => $line->id])->one();

        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        switch ($user->type) {
            case User::TYPE_DRIVER:
                $trip->driver_comment = Yii::$app->mv->gt("Посадка подтверждена водителем", [], false);
                break;
            case User::TYPE_PASSENGER:
                $this->validateBodyParams(['code']);
                $v_id = intval($this->body->code);
                if ($v_id != $trip->vehicle_id) $this->module->setError(422,
                    '_code', Yii::$app->mv->gt("Не правильный код", [], false));
                $trip->passenger_comment = Yii::$app->mv->gt("Посадка подтверждена пассажиром", [], false);
                break;
        }

        $trip->status = Trip::STATUS_WAY;
        $trip->start_time = time();

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422,
                        '_trip.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422,
                '_trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $user->id]);
        if (!$device) $this->module->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
        $socket = new SocketPusher(['authkey' => $device->auth_token]);
        $socket->push(base64_encode(json_encode([
            'action' => "acceptPassengerSeat",
            'notifications' => Notifications::create(
                Notifications::NTD_TRIP_SEAT,
                [$trip->user_id],
                "Хорошей поездки, {$user->fullName}",
                $user->id
            ),
            'data' => ['message_id' => time(), 'addressed' => [$line->driver_id, $trip->user_id], 'trip' => $trip->toArray()]
        ])));

        RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
            'AND',
            ['user_id' => $trip->user_id],
            ['type' => [RestFul::TYPE_PASSENGER_ACCEPT, RestFul::TYPE_PASSENGER_ACCEPT_SEAT]]
        ]);

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionRatePassenger($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['trip_id', 'passenger_rating']);

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::findOne($this->body->trip_id);
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $passenger = User::findOne($trip->user_id);
        if (!$passenger) $this->module->setError(422, '_passenger', Yii::$app->mv->gt("Не найден", [], false));

        $trip->passenger_rating = floatval($this->body->passenger_rating);

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422, 'trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $passenger->save();

        $notifications = Notifications::create(Notifications::NTP_TRIP_RATING, [$trip->user_id], '', $user->id);
        foreach ($notifications as $notification) Notifications::send($notification);

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCommentPassenger($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['trip_id', 'passenger_comment']);

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::findOne($this->body->trip_id);
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->passenger_comment = $this->body->passenger_comment;

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422, 'trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $notifications = Notifications::create(Notifications::NTP_TRIP_REVIEW, [$trip->user_id], '', $user->id);
        foreach ($notifications as $notification) Notifications::send($notification);

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionRateDriver($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['trip_id']);

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::findOne($this->body->trip_id);
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $driver = User::findOne($trip->driver_id);
        if (!$driver) $this->module->setError(422, '_driver', Yii::$app->mv->gt("Не найден", [], false));

        if (isset($this->body->driver_rating)) {
            $trip->driver_rating = $this->body->driver_rating;
            $notifications = Notifications::create(Notifications::NTD_TRIP_REVIEW, [$trip->driver_id], '', $user->id);
            foreach ($notifications as $notification) Notifications::send($notification);
        }
        if (isset($this->body->driver_comment)) {
            $trip->driver_comment = $this->body->driver_comment;
            $notifications = Notifications::create(Notifications::NTD_TRIP_REVIEW, [$trip->driver_id], '', $user->id);
            foreach ($notifications as $notification) Notifications::send($notification);
        }

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422, 'trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $driver->save();

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionPassengerComments($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $trips = $this->getUserTrips($id);
        $reviews = [];

        /** @var \app\modules\api\models\Trip $trip */
        if ($trips && count($trips) > 0) foreach ($trips as $trip) {
            if (!empty($trip->passenger_comment) && intval($trip->passenger_rating)) $reviews[] = [
                'rating' => $trip->passenger_rating,
                'comment' => $trip->passenger_comment,
                'date' => $trip->created_at,
                'route' => $trip->route->toArray(),
            ];
        }

        $this->module->data = $reviews;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionDriverComments($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $trips = $this->getDriverTrips($id);
        $reviews = [];

        /** @var \app\modules\api\models\Trip $trip */
        if ($trips && count($trips) > 0) foreach ($trips as $trip) {
            if (!empty($trip->driver_comment) && intval($trip->driver_rating)) $reviews[] = [
                'rating' => $trip->driver_rating,
                'comment' => $trip->driver_comment,
                'date' => $trip->created_at,
                'route' => $trip->route->toArray(),
            ];
        }

        $this->module->data = $reviews;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionPassengerTrips()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $trips_list_past = [];
        $trips_list_feature = [];
        $trips_list_current = [];

        $trips = Trip::find()->where(['user_id' => $user->id])->orderBy(['created_at' => SORT_DESC])->all();

        /** @var \app\modules\api\models\Trip $trip */
        if ($trips && count($trips)) foreach ($trips as $trip) {

            $past = [
                Trip::STATUS_FINISHED,
                //Trip::STATUS_CANCELLED,
                //Trip::STATUS_CANCELLED_DRIVER
            ];

            $feature = [
                Trip::STATUS_SCHEDULED
            ];

            $current = [
                Trip::STATUS_CREATED,
                Trip::STATUS_WAITING,
                Trip::STATUS_WAY
            ];

            $array_trip = $trip->toArray();

            if (in_array($trip->status, $past) && empty($trip->schedule)) {
                $wait_time = $trip->waiting_time - $trip->queue_time;
                $way_time = $trip->finish_time - $trip->start_time;
                $array_trip += [
                    'wait_time' => ($wait_time < 0) ? 0 : $wait_time,
                    'way_time' => ($way_time < 0) ? 0 : $way_time,
                ];
                $trips_list_past[] = $array_trip;
            }

            if (in_array($trip->status, $feature) || ($trip->status == Trip::STATUS_CREATED && $trip->queue_time >= time())) {

                $queue_times = [];
                $queue_time = $array_trip['queue_time'];
                $schedule = $array_trip['schedule'];

                if (!empty($schedule)) {

                    foreach ($schedule as $day) {
                        $target = Yii::$app->params['weekdays'][$day];
                        $date = new \DateTime();
                        $date->modify("next $target");
                        $time = date('H:i:s', $queue_time);
                        $args = explode(':', $time);
                        $date->setTime($args[0], $args[1], $args[2]);
                        $queue_times[] = $date->getTimestamp();
                    }
                    sort($queue_times);
                    $array_trip['queue_time'] = $queue_times[0];
                }

                $trips_list_feature[] = $array_trip;

            }

            if (in_array($trip->status, $current) && $trip->queue_time <= time()) $trips_list_current[] = $array_trip;
        }

        $this->module->data['trips']['past'] = $trips_list_past;
        $this->module->data['trips']['feature'] = $trips_list_feature;
        $this->module->data['trips']['current'] = $trips_list_current;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionPassengersRoute($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\modules\api\models\Route $route */
        $route = \app\modules\api\models\Route::findOne($line->route_id);
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        $trips = Trip::find()->andWhere([
            'AND',
            ['=', 'line_id', $line->id],
            ['=', 'driver_id', $line->driver_id],
            ['=', 'status', Line::STATUS_WAITING]
        ])->all();

        $checkpoints = [];
        if ($trips && count($trips) > 0) foreach ($trips as $trip) {
            /** @var \app\modules\api\models\Trip $trip */

            $checkpoints[$trip->startpoint->id][] = [
                'trip' => $trip->toArray(),
                'position' => $trip->position
            ];
        }

        $passengers_seat = Trip::find()->andWhere([
            'AND',
            ['=', 'line_id', $line->id],
            ['=', 'driver_id', $line->driver_id],
            ['=', 'status', Line::STATUS_IN_PROGRESS]
        ])->all();

        $passengers = [];
        if ($passengers_seat && count($passengers_seat) > 0) foreach ($passengers_seat as $passenger) {
            $passengers[] = $passenger->toArray();
        }


        $this->module->data['line'] = $line->toArray();
        $this->module->data['checkpoints'] = array_values($checkpoints);
        $this->module->data['passengers'] = $passengers;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTrips()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $trips = [
            'rating' => $user->getRating(),
            'photo_url' => $user->getImageFile(),
            'trips' => []
        ];

        $tariff = 0;

        $lines = \app\modules\api\models\Line::find()->where([
            'AND',
            ['=', 'status', Line::STATUS_FINISHED],
            ['=', 'driver_id', $user->id]
        ])->all();

        /** @var \app\modules\api\models\Line $line */
        foreach ($lines as $line) {

            /** @var Trip[] $passengers */
            $passengers = Trip::find()->where(['line_id' => $line->id, 'status' => Trip::STATUS_FINISHED])->all();

            $seats = 0;
            $amount = 0;

            if (!empty($passengers)) array_map(function ($item) use (&$seats, &$amount) {
                /** @var Trip $item */
                $seats += $item->seats;
                $amount += $item->amount;
            }, $passengers);

            $vehicle_type = $line->vehicle->type;

            $trips['trips'][] = [
                'created' => $line->created_at,
                'passengers' => intval($seats),
                'vehicle_photo_url' => $vehicle_type->image,
                'vehicle_type' => $vehicle_type->toArray(),
                'start_time' => $line->starttime,
                'end_time' => $line->endtime,
                'wait_time' => intval($line->starttime - $line->created_at),
                'way_time' => intval($line->endtime - $line->starttime),
                'startpoint' => $line->startPoint->toArray(),
                'endpoint' => $line->endPoint->toArray(),
                'route' => $line->route->toArray(),
                'tariff' => floatval($amount)
            ];

            $tariff += $amount;

        }

        $trips['tariff'] = floatval(round($tariff * 0.8, 2));

        $this->module->data = $trips;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionPassengers($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne($id);
        if (!$line) $this->module->setError(422, 'line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trips = Trip::find()->where([
            'AND',
            ['=', 'route_id', $line->route_id],
            ['=', 'vehicle_id', $line->vehicle_id],
            ['=', 'driver_id', $line->driver_id],
            ['=', 'line_id', $line->id]
        ])->all();
        if (!$trips) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $_passengers = [];
        /** @var \app\models\Trip $passenger */
        foreach ($trips as $passenger) $_passengers[] = [
            'passenger' => $passenger->toArray(),
            'seats' => intval($passenger->seats)
        ];

        $this->module->data = $_passengers;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionAcceptDeparture($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()->where(['route_id' => $line->route_id, 'driver_id' => $line->driver_id, 'user_id' => $user->id])->one();
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->status = Trip::STATUS_WAY;

        $this->module->data['trip'] = $trip->toArray();
        $this->module->data['line'] = $line->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionLuggageType()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        /** @var \app\models\LuggageType $luggage_type */
        $luggage_types = LuggageType::find()->where(['status' => LuggageType::STATUS_ACTIVE])->all();
        if ($luggage_types && count($luggage_types) > 0) foreach ($luggage_types as $luggage_type) {
            $this->module->data['types'][] = $luggage_type->toArray();
        }

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTaxi()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['address', 'checkpoint']);

        /** @var \app\models\Checkpoint $checkpoint */
        $checkpoint = \app\models\Checkpoint::find()->andWhere([
            'AND',
            ['=', 'id', $this->body->checkpoint],
            ['=', 'status', \app\models\Checkpoint::STATUS_ACTIVE],
            ['=', 'type', \app\models\Checkpoint::TYPE_STOP]
        ])->one();

        if (!$checkpoint) $this->module->setError(422, '_checkpoint', Yii::$app->mv->gt("Не найден", [], false));

        $taxi = new Taxi();
        $taxi->user_id = $user->id;
        $taxi->status = $taxi::STATUS_NEW;
        $taxi->address = $this->body->address;
        $taxi->checkpoint = $checkpoint->id;

        if (!$taxi->validate() || !$taxi->save()) {
            if ($taxi->hasErrors()) {
                foreach ($taxi->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422, 'taxi.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422, '_taxi', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data['taxi'] = $taxi->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCalculateDriverTariff($id)
    {

        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['seats']);

        $tariff = (object)$this->calculateTariff($id);

        $this->module->data['commission'] = ($tariff->base_tariff * 1.5 * $this->body->seats) / 10;
        $this->module->data['one'] = $tariff->tariff;
        $this->module->setSuccess();
        $this->module->sendResponse();

    }

    public function actionCalculatePassengerTariff($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['seats', 'checkpoint']);

        $seats = $this->body->seats;

        if (isset($this->body->luggage) && !empty($this->body->luggage)) {
            $luggages = $this->body->luggage;
            foreach ($luggages as $luggage) {
                $luggage = LuggageType::findOne($luggage);
                if (!$luggage) $this->module->setError(422, '_luggage', Yii::$app->mv->gt("Не найден", [], false));
                if ($luggage->need_place) $seats += $luggage->seats;
            }
        }

        $tariff = (object)$this->calculateTariff($id);

        $this->module->data['tariff'] = $tariff->tariff * $seats;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    /** CORE METHODS | PROTECTED */

    /**
     * @param $id
     * @param string $type
     * @return array|bool|\yii\db\ActiveRecord[]
     */
    protected function getTrips($id, $type = 'user')
    {
        switch ($type) {
            case 'user':
                return $this->getUserTrips($id);
                break;

            case 'driver':
                return $this->getDriverTrips($id);
                break;
        }

        return false;
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    protected function getUserTrips($id)
    {
        return Trip::find()->where(['user_id' => $id])->all();
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord[]
     */
    protected function getDriverTrips($id)
    {
        return Trip::find()->where(['driver_id' => $id])->all();
    }

    /**
     * @param $route_id
     * @return float|int
     */
    protected function getRate($route_id)
    {
        /** @var \app\models\Line $line */
        $lines = Line::find()->where(['route_id' => $route_id, 'status' => Line::STATUS_QUEUE])->all();

        $seats = 0;
        foreach ($lines as $line) $seats += $line->freeseats;

        $passengers = Trip::find()->where(['route_id' => $route_id, 'status' => Trip::STATUS_CREATED])->count();

        if ($seats == 0) $rate = 1.5;
        elseif ($passengers == 0) $rate = 1;
        else {
            $hard_rate = round($passengers / $seats, 2);

            if ($hard_rate <= .35) $rate = 1;
            elseif ($hard_rate >= .35 && $hard_rate <= .6) $rate = 1.1;
            elseif ($hard_rate >= .6 && $hard_rate <= .7) $rate = 1.2;
            elseif ($hard_rate >= .7 && $hard_rate <= .8) $rate = 1.3;
            elseif ($hard_rate >= .8 && $hard_rate <= .9) $rate = 1.4;
            else $rate = 1.5;
        }

        return $rate;
    }

    /**
     * @param $id
     * @param $checkpoint
     * @param bool|Taxi $taxi
     * @return array
     */
    protected function calculateTariff($id)
    {
        $rate = $this->getRate($id);

        /** @var \app\models\Route $route */
        $route = Route::findOne(['status' => Route::STATUS_ACTIVE, 'id' => $id]);
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        $tariff = $route->base_tariff * $rate;

        return ['base_tariff' => $route->base_tariff, 'tariff' => $tariff];
    }

}
