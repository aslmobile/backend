<?php namespace app\modules\api\controllers;

use app\models\Countries;
use app\models\Line;
use app\models\LuggageType;
use app\models\Route;
use app\models\TariffDependence;
use app\models\Taxi;
use app\models\TripLuggage;
use app\modules\admin\models\Checkpoint;
use app\modules\api\models\Trip;
use app\modules\api\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\modules\api\models\City;

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

                            'accept-arrive',
                            'passenger-comments',
                            'driver-comments',
                            'trips',
                            'passengers',
                            'checkpoint-arrived',
                            'luggage-type',
                            'taxi',
                            'queue'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'cities'  => ['GET'],
                    'passengers'  => ['GET'],
                    'accept-arrive' => ['POST'],
                    'checkpoint-arrived' => ['POST'],
                    'taxi' => ['POST'],
                    'luggage-type' => ['GET'],
                    'queue' => ['PUT']
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

    public function actionAcceptArrive($id)
    {
        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $line->status = Line::STATUS_IN_PROGRESS;
        $line->save();

        $this->module->data['line'] = $line->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionAcceptSeat($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['passenger_id']);

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()->where(['route_id' => $line->route_id, 'driver_id' => $line->driver_id, 'user_id' => $this->body->passenger_id])->one();
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->status = Trip::STATUS_TRIP;

        if (!$trip->validate() || !$trip->save())
        {
            if ($trip->hasErrors())
            {
                foreach ($trip->errors as $field => $error_message)
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionArriveEndpoint($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['cancel_reason_trip', 'cancel_reason_line']);

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trips = Trip::find()->andWhere(['route_id' => $line->route_id, 'vehicle_id' => $line->vehicle_id, 'driver_id' => $line->driver_id])->all();
        if (!$trips) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $_trips = [];
        foreach ($trips as $trip)
        {
            $trip->status = Trip::STATUS_ENDED;

            if (!$trip->validate() || !$trip->save())
            {
                if ($trip->hasErrors())
                {
                    foreach ($trip->errors as $field => $error_message)
                        $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                    $this->module->sendResponse();
                }
                else $this->module->setError(422, 'trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
            }

            $_trips[] = $trip->toArray();
        }

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trips'] = $_trips;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionRatePassenger($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['passenger_id', 'passenger_rating', 'driver_comment']);

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()->where(['route_id' => $line->route_id, 'driver_id' => $line->driver_id, 'user_id' => $this->body->passenger_id])->one();
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->passenger_rating = floatval($this->body->passenger_rating);
        $trip->driver_comment = $this->body->driver_comment;

        if (!$trip->validate() || !$trip->save())
        {
            if ($trip->hasErrors())
            {
                foreach ($trip->errors as $field => $error_message)
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, 'trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data['line'] = $line->toArray();
        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCheckpointArrived()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['checkpoint']);

        $checkpoint = Checkpoint::findOne(intval($this->body->checkpoint));
        if (!$checkpoint) $this->module->setError(422, 'checkpoint', Yii::$app->mv->gt("Не найден", [], false));

        // TODO: Отправить на сокет сообщение что водитель подъехал к checkpoint

        $this->module->data = $checkpoint->toArray();
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
        if ($trips && count($trips) > 0) foreach ($trips as $trip)
        {
            if (!empty($trip->passenger_comment) && intval($trip->passenger_rating)) $reviews[] = [
                'rating' => $trip->passenger_rating,
                'comment' => $trip->passenger_comment
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
        if ($trips && count($trips) > 0) foreach ($trips as $trip)
        {
            if (!empty($trip->passenger_comment) && intval($trip->passenger_rating)) $reviews[] = [
                'rating' => $trip->passenger_rating,
                'comment' => $trip->passenger_comment
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

        $data_trips = [];
        $lines = \app\modules\api\models\Line::find()->andWhere([
            'AND',
            ['=', 'status', Line::STATUS_FINISHED],
            ['=', 'driver_id', $user->id]
        ])->all();

        echo '<pre>' . print_r($lines, true) . '</pre>'; exit;

        foreach ($lines as $line)
        {
            /** @var \app\modules\api\models\Line $line */
            $passengers = Trip::find()->where(['line_id' => $line->id])->count();

            $data_trips[] = [
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

        $trips['trips'] = $data_trips;
        $trips['tariff'] = floatval(round($tariff * 0.8, 2));

//        echo '<pre>' . print_r(array_values($trips), true) . '</pre>'; exit;

        $this->module->data = $trips;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTest($id)
    {
        $tariff = $this->calculatePassengerTariff($id);

        $this->module->data['tariff'] = $tariff;
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
        $trips = Trip::find()->andWhere(['route_id' => $line->route_id, 'vehicle_id' => $line->vehicle_id, 'driver_id' => $line->driver_id])->all();
        if (!$trips) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $_passengers = [];
        /** @var \app\models\Trip $passenger */
        foreach ($trips as $passenger) $_passengers[] = [
            'passenger' => $passenger->user->toArray(),
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

        $trip->status = Trip::STATUS_TRIP;

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
        if ($luggage_types && count($luggage_types) > 0) foreach ($luggage_types as $luggage_type)
        {
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

        if (!$taxi->validate() || !$taxi->save())
        {
            if ($taxi->hasErrors())
            {
                foreach ($taxi->errors as $field => $error_message)
                    $this->module->setError(422, 'taxi.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, '_taxi', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data['taxi'] = $taxi->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionQueue()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['country', 'checkpoint', 'endpoint', 'route', 'time', 'seats', 'luggage', 'taxi', 'comment', 'schedule', 'payment_type']);

        $country = Countries::findOne($this->body->country);
        if (!$country) $this->module->setError(422, '_country', Yii::$app->mv->gt("Не найден", [], false));

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
            ['=', 'id', $this->body->endpoint]
        ])->one();
        if (!$endpoint) $this->module->setError(422, '_endpoint', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Route $route */
        $route = Route::find()->andWhere([
            'AND',
            ['=', 'status' , Route::STATUS_ACTIVE],
            ['=', 'id', $this->body->route]
        ])->one();
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        $seats = $this->body->seats;
        $_luggages = [];
        $luggages = $this->body->luggage;
        if (is_array($luggages) && count($luggages) > 0) foreach ($luggages as $luggage)
        {
            $luggage = LuggageType::findOne($luggage);
            if (!$luggage) $this->module->setError(422, '_luggage', Yii::$app->mv->gt("Не найден", [], false));
            $_luggages[] = $luggage->toArray();

            if ($luggage->need_place) $seats++;
        }

        $luggage_unique = false;
        if ($_luggages && count($_luggages) > 0)
        {
            foreach ($_luggages as $luggage) $luggage_unique .= $luggage['id'] . '+';
            $luggage_unique .= $user->id . '+' . $route->id;
            $luggage_unique = hash('sha256', md5($luggage_unique) . time());
        }

        $taxi = false;
        if ($this->body->taxi)
        {
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
        $trip->endpoint_id = $endpoint->id;
        $trip->payment_status = Trip::PAYMENT_STATUS_WAITING;
        $trip->passenger_description = $this->body->comment;
        $trip->need_taxi = $this->body->taxi ? 1 : 0;
        $trip->start_time = $this->body->time == -1 ? time() + 1800 : $this->body->time;

        if ($taxi)
        {
            $trip->taxi_status = $taxi->status;
            $trip->taxi_address = $taxi->address;
            $trip->taxi_time = time() + 900;
        }

        if ($this->body->schedule)
        {
            $trip->scheduled = 1;
            $trip->schedule_id = 0;
        }
        else $trip->scheduled = 0;

        if ($luggage_unique)
        {
            $trip->luggage_unique_id = (string) $luggage_unique;

            /** @var \app\models\TripLuggage $luggage */
            if ($_luggages && count ($$_luggages) > 0) foreach ($_luggages as $luggage)
            {
                if ($luggage['need_place'])
                {
                    $tariff = $this->calculateLuggageTariff($route->id);
                    $amount = (int) intval($luggage['seats']) * (float) floatval($tariff);
                }
                else $amount = (float) floatval(0.0);

                $_luggage = new TripLuggage();
                $_luggage->unique_id = (string) $luggage_unique;
                $_luggage->amount = (float) floatval($amount);
                $_luggage->status = (int) 0;
                $_luggage->need_place = (int) intval($luggage['need_place']);
                $_luggage->seats = (int) intval($luggage['seats']);
                $_luggage->currency = (string) "₸";
                $_luggage->luggage_type = (int) intval($luggage['id']);

                $_luggage->save(false);
            }
        }

        $trip->driver_id = 0;
        $trip->vehicle_id = 0;
        $trip->line_id = 0;

        if (!$trip->validate() || !$trip->save())
        {
            if ($trip->hasErrors())
            {
                foreach ($trip->errors as $field => $error_message)
                    $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    /** CORE METHODS | PROTECTED */

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
        else
        {
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
            'route_id'              => $route->id,
            'start_checkpoint_id'   => $checkpoint_start->id,
            'end_checkpoint_id'     => $checkpoint_end->id
        ])->one();

        if (!$dependence)
        {
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

    protected function calculateLuggageTariff($id)
    {
        $rate = $this->getRate($id);
        $taxi_tariff = 0;

        /** @var \app\models\Route $route */
        $route = Route::find()->where(['id' => $id])->one();
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        $tariff = $route->base_tariff * $rate;

        return (object) [
            'base_tariff' => $route->base_tariff,
            'tariff' => $tariff
        ];
    }

    protected function getTrips($id, $type = 'user')
    {
        switch ($type)
        {
            case 'user': return $this->getUserTrips($id);
                break;

            case 'driver': return $this->getDriverTrips($id);
                break;
        }

        return false;
    }

    protected function getUserTrips($id)
    {
        return Trip::find()->where(['user_id' => $id])->all();
    }

    protected function getDriverTrips($id)
    {
        return Trip::find()->where(['driver_id' => $id])->all();
    }
}