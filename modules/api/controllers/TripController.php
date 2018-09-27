<?php namespace app\modules\api\controllers;

use app\components\Socket\SocketPusher;
use app\models\Countries;
use app\models\Line;
use app\models\LuggageType;
use app\models\Route;
use app\models\TariffDependence;
use app\models\Taxi;
use app\models\TripLuggage;
use app\models\User;
use app\modules\admin\models\Checkpoint;
use app\modules\api\models\Devices;
use app\modules\api\models\Trip;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

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
                            'test',
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
                            'queue',
                            'passengers-route',
                            'passenger-trips',
                            'decline-passenger',
                            'cancel-trip-queue',
                            'rate-passenger',
                            'comment-passenger',
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'calculate-passenger-tariff' => ['GET'],
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
                    'decline-passenger' => ['DELETE'],
                    'cancel-trip-queue' => ['POST'],
                    'rate-passenger' => ['POST'],
                    'comment-passenger' => ['POST'],
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

    public function actionQueue()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['country', 'checkpoint', 'route', 'time', 'seats', 'taxi', 'payment_type']);

        $country = Countries::findOne($this->body->country);
        if (!$country) $this->module->setError(422, '_country', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Route $route */
        $route = Route::find()->andWhere([
            'AND',
            ['=', 'status', Route::STATUS_ACTIVE],
            ['=', 'id', $this->body->route]
        ])->one();
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Checkpoint $checkpoint */
        $checkpoint = \app\models\Checkpoint::find()->andWhere([
            'AND',
            ['=', 'type', \app\models\Checkpoint::TYPE_STOP],
            ['=', 'status', \app\models\Checkpoint::STATUS_ACTIVE],
            ['=', 'id', $this->body->checkpoint]
        ])->one();
        if (!$checkpoint) $this->module->setError(422, '_checkpoint', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Checkpoint $endpoint */
        $endpoint = \app\models\Checkpoint::find()->andWhere([
            'AND',
            ['=', 'type', \app\models\Checkpoint::TYPE_END],
            ['=', 'status', \app\models\Checkpoint::STATUS_ACTIVE],
            ['=', 'route', $route->id]
        ])->one();
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
        if ($this->body->taxi) {
            $taxi = Taxi::findOne($this->body->taxi);
            if (!$taxi) $this->module->setError(422, '_taxi', Yii::$app->mv->gt("Не найден", [], false));
        }

        $trip = new Trip();
        $trip->status = Trip::STATUS_WAITING;
        $trip->user_id = $user->id;
        $trip->tariff = $this->calculatePassengerTariff($route->id, $checkpoint->id, $endpoint->id)['tariff'];
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
        $trip->start_time = $this->body->time == -1 ? time() + 1800 : $this->body->time;

        if ($taxi) {
            $trip->taxi_status = $taxi->status;
            $trip->taxi_address = $taxi->address;
            $trip->taxi_time = time() + 900;
        }

        if ($this->body->schedule) {

            // TODO: Сделать расписание
            $trip->scheduled = 1;
            $trip->schedule_id = 0;

        } else $trip->scheduled = 0;

        if ($luggage_unique) {
            $trip->luggage_unique_id = (string)$luggage_unique;

            /** @var \app\models\TripLuggage $luggage */
            if ($_luggages && count($$_luggages) > 0) foreach ($_luggages as $luggage) {
                if ($luggage['need_place']) {
                    $tariff = $this->calculateLuggageTariff($route->id);
                    $amount = (int)intval($luggage['seats']) * (float)floatval($tariff);
                } else $amount = (float)floatval(0.0);

                $_luggage = new TripLuggage();
                $_luggage->unique_id = (string)$luggage_unique;
                $_luggage->amount = (float)floatval($amount);
                $_luggage->status = (int)0;
                $_luggage->need_place = (int)intval($luggage['need_place']);
                $_luggage->seats = (int)intval($luggage['seats']);
                $_luggage->currency = (string)"₸";
                $_luggage->luggage_type = (int)intval($luggage['id']);

                $_luggage->save(false);

                $trip->seats += $_luggage->seats;
                $trip->amount += $_luggage->amount;
            }
        }

        $trip->driver_id = 0;
        $trip->vehicle_id = 0;
        $trip->line_id = 0;

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message)
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            } else $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

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
        $trip = Trip::find()->andWhere([
            'AND',
            ['=', 'user_id', $user->id],
            ['=', 'status', Trip::STATUS_WAITING]
        ])->one();
        if (!$trip) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        if (!isset ($this->body->cancel_reason)) $this->body->cancel_reason = 0;

        $trip->status = Trip::STATUS_CANCELLED;
        $trip->cancel_reason = $this->body->cancel_reason;
        $trip->save();

        $this->module->data['trip'] = $trip->toArray();
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

        $trips = Trip::find()->andWhere([
            'AND',
            ['=', 'user_id', $user->id]
        ])->all();

        /** @var \app\modules\api\models\Trip $trip */
        if ($trips && count($trips)) foreach ($trips as $trip) {
            $past = [
                Trip::STATUS_FINISHED,
                Trip::STATUS_CANCELLED,
                Trip::STATUS_CANCELLED_DRIVER
            ];

            $feature = [
                Trip::STATUS_CREATED,
                Trip::STATUS_SCHEDULED
            ];

            $current = [
                Trip::STATUS_WAITING,
                Trip::STATUS_WAY
            ];

            if (in_array($trip->status, $past)) $trips_list_past[] = $trip->toArray();
            if (in_array($trip->status, $feature)) $trips_list_feature[] = $trip->toArray();
            if (in_array($trip->status, $current)) $trips_list_current[] = $trip->toArray();
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

    /**
     * Take a trip on a certain line by the passenger
     * @param $id
     */
    public function actionAcceptPassenger($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        /** @var \app\modules\api\models\Line $line */
        $line = \app\modules\api\models\Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $this->prepareBody();
        $this->validateBodyParams(['trip_id']);

        /** @var \app\modules\api\models\Trip $trip */
        $trip = Trip::findOne(['status' => [Trip::STATUS_WAITING], 'id' => $this->body->trip_id]);

        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->line_id = $line->id;
        $trip->driver_id = $line->driver_id;
        $trip->vehicle_id = $line->vehicle_id;

        if ($line->freeseats > $trip->seats) {
            $line->freeseats = $line->freeseats - $trip->seats;
        } else if ($line->freeseats == $trip->seats) {
            $line->freeseats = 0;
            $line->status = Line::STATUS_WAITING;
        } else {
            $this->module->setError(400, '_seats', Yii::$app->mv->gt("Не достаточно свободных мест", [], false));
        };

        $trip->save();
        $line->save();

        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $user->id]);
        if (!$device) $this->module->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
        $socket = new SocketPusher(['authkey' => $device->auth_token]);
        $socket->push(base64_encode(json_encode([
            'action' => "acceptPassengerTrip",
            'data' => ['message_id' => time(), 'addressed' => [$line->driver_id]]
        ])));

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trip'] = $trip->toArray();
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

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()->where(['id' => $this->body->trip_id, 'status' => Trip::STATUS_WAITING])->one();

        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->status = Trip::STATUS_WAY;
        $trip->start_time = time();
        switch ($user->type) {
            case User::TYPE_DRIVER:
                $trip->driver_comment = Yii::$app->mv->gt("Посадка подтверждена водителем", [], false);
                break;
            case User::TYPE_PASSENGER:
                $trip->passenger_comment = Yii::$app->mv->gt("Посадка подтверждена пассажиром", [], false);
                break;
        }

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message)
                    $this->module->setError(422,
                        '_trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
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
            'data' => ['message_id' => time(), 'addressed' => [$line->driver_id]]
        ])));

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    /**
     * Decline trip by passenger
     * @param $id
     */
    public function actionDeclinePassenger($id)
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
        if (!$trip) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $addressed = [];

        switch ($user->type) {
            case User::TYPE_DRIVER:
                $trip->status = Trip::STATUS_CANCELLED_DRIVER;
                $addressed[] = $trip->user_id;
                break;
            case User::TYPE_PASSENGER:
                $trip->status = Trip::STATUS_CANCELLED;
                $addressed[] = $line->driver_id;
                break;
            default:
                $trip->status = Trip::STATUS_CANCELLED;
                $addressed[] = $trip->user_id;
                $addressed[] = $line->driver_id;
        }

        $trip->cancel_reason = isset($this->body->cancel_reason) ? $this->body->cancel_reason : 0;
        $line->freeseats += $trip->seats;

        $trip->save();
        $line->save();

        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $user->id]);
        if (!$device) $this->module->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
        $socket = new SocketPusher(['authkey' => $device->auth_token]);
        $socket->push(base64_encode(json_encode([
            'action' => "declinePassengerTrip",
            'data' => ['message_id' => time(), 'addressed' => $addressed]
        ])));

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

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var Checkpoint $checkpoint */
        $checkpoint = Checkpoint::findOne(intval($this->body->checkpoint_id));
        if (!$checkpoint) $this->module->setError(422, 'checkpoint', Yii::$app->mv->gt("Не найден", [], false));

        $data = [
            'line' => $line->toArray(),
            'checkpoint' => $checkpoint->toArray(),
        ];

        if ($checkpoint->type == Checkpoint::TYPE_END) {

            /** @var \app\models\Trip $trip */
            $trips = Trip::find()->andWhere([
                'route_id' => $line->route_id,
                'vehicle_id' => $line->vehicle_id,
                'driver_id' => $line->driver_id,
                'status' => Trip::STATUS_WAY,
                //'payment_status' => Trip::PAYMENT_STATUS_PAID
            ])->all();

            if (!$trips) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найдены", [], false));

            $_trips = [];
            $total = ['cash' => 0, 'card' => 0];
            foreach ($trips as $trip) {

                $trip->status = Trip::STATUS_FINISHED;

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
                        foreach ($trip->errors as $field => $error_message)
                            $this->module->setError(422,
                                'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                        $this->module->sendResponse();
                    } else $this->module->setError(422,
                        'trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
                }

                $_trips[] = $trip->toArray();
            }

            $line->status = Line::STATUS_FINISHED;
            $line->update();

            $data += [
                'trips' => $_trips,
                'total' => $total,
            ];
        }

        /** @var \app\models\Devices $device */
        $device = Devices::findOne(['user_id' => $user->id]);
        if (!$device) $this->module->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
        $socket = new SocketPusher(['authkey' => $device->auth_token]);
        $socket->push(base64_encode(json_encode([
            'action' => "checkpointArrived",
            'data' => ['message_id' => time(), 'line' => $line, 'checkpoint' => $checkpoint]
        ])));

        $this->module->data = $data;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionRatePassenger($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['passenger_id', 'passenger_rating']);

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()->where(['route_id' => $line->route_id, 'driver_id' => $line->driver_id, 'user_id' => $this->body->passenger_id])->one();
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->passenger_rating = floatval($this->body->passenger_rating);

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message)
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            } else $this->module->setError(422, 'trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

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
        $this->validateBodyParams(['passenger_id', 'driver_comment']);

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()->where(['route_id' => $line->route_id, 'driver_id' => $line->driver_id, 'user_id' => $this->body->passenger_id])->one();
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->driver_comment = $this->body->driver_comment;

        if (!$trip->validate() || !$trip->save()) {
            if ($trip->hasErrors()) {
                foreach ($trip->errors as $field => $error_message)
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            } else $this->module->setError(422, 'trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

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

        $lines = \app\modules\api\models\Line::find()->andWhere([
            'AND',
            ['=', 'status', Line::STATUS_FINISHED],
            ['=', 'driver_id', $user->id]
        ])->all();

        foreach ($lines as $line) {
            /** @var \app\modules\api\models\Line $line */
            $passengers = Trip::find()->where(['line_id' => $line->id])->count();

            $trips['trips'][] = [
                'created' => $line->created_at,
                'passengers' => intval($passengers),
                'vehicle_photo_url' => $line->vehicle->photoUrl,
                'vehicle_type' => $line->vehicle->type->toArray(),
                'start_time' => $line->starttime,
                'end_time' => $line->endtime,
                'wait_time' => intval($line->starttime - $line->created_at),
                'way_time' => intval($line->endtime - $line->starttime),
                'startpoint' => $line->startPoint->toArray(),
                'endpoint' => $line->endPoint->toArray(),
                'route' => $line->route->toArray(),
                'tariff' => floatval(round($line->tariff * 0.8, 2))
            ];

            $tariff += $line->tariff;
        }

        $trips['tariff'] = floatval(round($tariff * 0.8, 2));

//        echo '<pre>' . print_r(array_values($trips), true) . '</pre>'; exit;

        $this->module->data = $trips;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionPassengers($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
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

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
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
                foreach ($taxi->errors as $field => $error_message)
                    $this->module->setError(422, 'taxi.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            } else $this->module->setError(422, '_taxi', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data['taxi'] = $taxi->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCalculatePassengerTariff()
    {

    }

    public function actionTest($id)
    {
        $tariff = $this->calculatePassengerTariff($id);

        $this->module->data['tariff'] = $tariff;
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
        $lines = Line::find()->andWhere([
            'AND',
            ['=', 'route_id', $route_id]
        ])->all();

        if (!$lines) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $seats = 0;
        foreach ($lines as $line) $seats += $line->freeseats;

        $passengers = Trip::find()->andWhere([
            'AND',
            ['=', 'route_id', $route_id],
            ['=', 'driver_id', 0]
        ])->count();

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
     * @param int $checkpoint_start
     * @param int $checkpoint_end
     * @return array
     */
    protected function calculatePassengerTariff($id, $checkpoint_start = 0, $checkpoint_end = 0)
    {
        $rate = $this->getRate($id);
        $taxi_tariff = 0; // TODO: Брать тариф на такси через админку

        /** @var \app\models\Route $route */
        $route = Route::find()->where(['id' => $id])->one();
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Checkpoint $checkpoint_start */
        $checkpoint_start = Checkpoint::find()->where(['id' => $checkpoint_start])->one();
        if (!$checkpoint_start) $this->module->setError(422, '_checkpoint', Yii::$app->mv->gt("Не найдена", [], false));

        /** @var \app\models\Checkpoint $checkpoint_end */
        $checkpoint_end = Checkpoint::find()->where(['id' => $checkpoint_end])->one();
        if (!$checkpoint_end) $this->module->setError(422, '_checkpoint', Yii::$app->mv->gt("Не найдена", [], false));

        $dependence = TariffDependence::find()->where([
            'route_id' => $route->id,
            'start_checkpoint_id' => $checkpoint_start->id,
            'end_checkpoint_id' => $checkpoint_end->id
        ])->one();

        if (!$dependence) {
            $dependence = new TariffDependence();
            $dependence->route_id = $route->id;
            $dependence->start_checkpoint_id = $checkpoint_start->id;
            $dependence->end_checkpoint_id = $checkpoint_end->id;
            $dependence->base_tariff = $route->base_tariff;
            $dependence->base_rate = 0;

            $dependence->save();
        }

        $tariff = $dependence->base_tariff * $rate + $taxi_tariff;

        return [
            'base_tariff' => $dependence->base_tariff,
            'tariff' => $tariff
        ];
    }

    /**
     * @param $id
     * @return object
     */
    protected function calculateLuggageTariff($id)
    {
        $rate = $this->getRate($id);
        $taxi_tariff = 0;

        /** @var \app\models\Route $route */
        $route = Route::find()->where(['id' => $id])->one();
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        $tariff = $route->base_tariff * $rate;

        return (object)[
            'base_tariff' => $route->base_tariff,
            'tariff' => $tariff
        ];
    }

    protected function calculateDriverTariff()
    {
        /**
         * Расчет тарифа по зависимостям:
         * - Базовый тариф
         * - Задолженость
         * - Спрос
         *
         * ((кол-во пассажиров / кол-во мест) * базовый тариф) * коофициент + задолженость
         */
    }

}
