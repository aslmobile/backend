<?php namespace app\modules\api\controllers;

use app\models\Checkpoint;
use app\models\Line;
use app\models\Route;
use app\modules\api\models\RestFul;
use app\modules\api\models\Trip;
use app\modules\api\models\Users;
use app\modules\api\models\Vehicles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\modules\api\models\City;

/** @property \app\modules\api\Module $module */
class LineController extends BaseController
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
                            'startpoints', 'endpoints', 'checkpoints',
                            'startpoints-route', 'endpoints-route', 'checkpoints-route',

                            'accept-arrive',
                            'route', 'handle-route-points',
                            'passenger-accept',

                            'update-line', 'passengers', 'seats',
                            'cancel', 'passenger-decline', 'on-line', 'calculate-tariff'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'startpoints'  => ['GET'],
                    'endpoints'  => ['GET'],
                    'checkpoints'  => ['GET'],
                    'startpoints-route'  => ['GET'],
                    'endpoints-route'  => ['GET'],
                    'checkpoints-route'  => ['GET'],

                    'accept-arrive' => ['POST'],
                    'handle-route-points' => ['POST'],
                    'route' => ['GET'],
                    'passenger-accept' => ['POST'],

                    'passengers'  => ['GET'],
                    'seats'  => ['GET'],
                    'update-line'  => ['PUT'],
                    'cancel' => ['DELETE'],
                    'on-line' => ['PUT'],
                    'passenger-decline' => ['DELETE'],
                    'calculate-tariff' => ['POST']
                ]
            ]
        ];
    }

    public function beforeAction($event)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        /**
         * Проверка прав на тип пользователя. Должен быть водителем, для доступа к методам
         */
//        if ($user->type != Users::TYPE_DRIVER)
//            $this->module->setError(403, 'user', Yii::$app->mv->gt("У пользователя нет прав на данное действие", [], false));

        return parent::beforeAction($event);
    }

    public function actionPassengerAccept($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $this->prepareBody();
        $this->validateBodyParams(['passenger']);

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()->andWhere([
            'AND',
            ['=', 'user_id', $user->id],
            ['=', 'line_id', $line->id]
        ])->one();

        if (!$trip) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $trip->status = Trip::STATUS_WAY;
        $trip->save();

        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionHandleRoutePoints($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $this->prepareBody();
        $this->validateBodyParams(['points', 'duration', 'distance']);

        $points = $this->body->points;
        $duration = $this->body->duration;
        $distance = $this->body->distance;

        $log = RestFul::find()->andWhere([
            'AND',
            ['=', 'user_id', $user->id],
            ['=', 'type', RestFul::TYPE_DRIVER_HANDLE_ROUTE]
        ])->one();

        if (!$log) $log = new RestFul();
        $log->type = RestFul::TYPE_DRIVER_HANDLE_ROUTE;
        $log->message = json_encode(['duration' => $duration, 'distance' => $distance, 'points' => array_values($points), 'line' => $line->id]);
        $log->user_id = $user->id;
        $log->uip = $_SERVER['REMOTE_ADDR'];
        $log->save();

        $this->module->data = array_values($points);
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionRoute($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        if (in_array($line->status, [Line::STATUS_IN_PROGRESS, Line::STATUS_WAITING]))
        {
            $passengers_trips = [];
            $trips = Trip::find()->andWhere([
                'AND',
                ['=', 'line_id', $line->id],
                ['=', 'driver_id', $line->driver_id],
                ['IN', 'status', [Trip::STATUS_WAITING, Trip::STATUS_WAY]]
            ])->all();

            $passengers = [
                'route' => [],
                'cabin' => [],
                'total' => [
                    'cabin' => 0,
                    'route' => 0,
                    'total' => 0
                ]
            ];
            $checkpoints = [];

            /** @var \app\models\Trip $trip */
            if ($trips && count ($trips) > 0) {
                foreach ($trips as $trip)
                {
                    // TODO: SORT BY WEIGHT OF CHECKPOINT
                    $passed_checkpoint = RestFul::find()->where([
                        'AND',
                        ['=', 'user_id', $user->id],
                        ['=', 'type', RestFul::TYPE_DRIVER_CHECKPOINT_ARRIVE],
                        ['=', 'message', json_encode(['status' => 'passed', 'checkpoint' => $trip->startpoint->id, 'line' => $line->id])]
                    ])->one();

                    if ($trip->status == Trip::STATUS_WAY)
                    {
                        $passengers['total']['cabin']++;
                        $passengers['cabin'][] = [
                            'id'    => $trip->user->id,
                            'name'  => $trip->user->fullName,
                            'phone' => $trip->user->phone,
                            'baggage'       => $trip->baggages,
                            'image_url'     => $trip->user->getImageFile(),
                            'payment_type'  => $trip->payment_type,
                            'seats'         => $trip->seats,
                            'comment'       => $trip->passenger_description,
                            'rating'        => $trip->passenger_rating
                        ];
                    }

                    if ($trip->status == Trip::STATUS_WAITING)
                    {
                        $passengers['total']['route']++;
                        $passengers['route'][] = [
                            'id'    => $trip->user->id,
                            'name'  => $trip->user->fullName,
                            'phone' => $trip->user->phone,
                            'baggage'       => $trip->baggages,
                            'image_url'     => $trip->user->getImageFile(),
                            'payment_type'  => $trip->payment_type,
                            'seats'         => $trip->seats,
                            'comment'       => $trip->passenger_description,
                            'rating'        => $trip->passenger_rating
                        ];
                    }

                    if (isset ($checkpoints[(int) $trip->startpoint->id]))
                    {
                        $checkpoints[(int) $trip->startpoint->id]['passengers'][] = [
                            'id' => $trip->user->id,
                            'name'  => $trip->user->fullName,
                            'phone' => $trip->user->phone,
                            'baggage'       => $trip->baggages,
                            'image_url'     => $trip->user->getImageFile(),
                            'payment_type'  => $trip->payment_type,
                            'seats'         => $trip->seats,
                            'comment'       => $trip->passenger_description,
                            'rating'        => $trip->passenger_rating
                        ];
                    }
                    else
                    {
                        $checkpoints[(int) $trip->startpoint->id] = [
                            'id'        => $trip->startpoint->id,
                            'title'     => $trip->startpoint->title,
                            'latitude'  => $trip->startpoint->latitude,
                            'longitude' => $trip->startpoint->longitude,
                            'weight'    => intval($trip->startpoint->weight),
                            'passed'    => $passed_checkpoint ? 1 : 0,
                            'passengers' => [
                                [
                                    'id' => $trip->user->id,
                                    'name'  => $trip->user->fullName,
                                    'phone' => $trip->user->phone,
                                    'baggage'       => $trip->baggages,
                                    'image_url'     => $trip->user->getImageFile(),
                                    'payment_type'  => $trip->payment_type,
                                    'seats'         => $trip->seats,
                                    'comment'       => $trip->passenger_description,
                                    'rating'        => $trip->passenger_rating
                                ]
                            ]
                        ];
                    }

                    $passengers['total']['total']++;
                }

                $passengers_trips['line'] = [
                    'id'    => $line->id,
                    'startpoint' => [
                        'id'        => $line->startPoint->id,
                        'longitude' => $line->startPoint->longitude,
                        'latitude'  => $line->startPoint->latitude
                    ],
                    'endpoint'  => [
                        'id'        => $line->endPoint->id,
                        'longitude' => $line->endPoint->longitude,
                        'latitude'  => $line->endPoint->latitude
                    ],
                    'tariff' => $line->tariff
                ];
                $passengers_trips['passengers']['route'] = array_values($passengers{'route'});
                $passengers_trips['passengers']['cabin'] = array_values($passengers{'cabin'});
                $passengers_trips['passengers']['total'] = $passengers['total'];
                $passengers_trips['checkpoints']    = array_values($checkpoints);

                $this->module->data = $passengers_trips;
                $this->module->setSuccess();
                $this->module->sendResponse();
            }
            else $this->module->setError(422, '_trips', Yii::t('app', "Данные по пассажирам не найдены"));
        }
        else $this->module->setError(422, '_line', Yii::$app->mv->gt("Маршрут не может быть построен. Не верный статус поездки.", [], false));
    }

    public function actionAcceptArrive($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $checkpoint = Checkpoint::findOne($id);
        if (!$checkpoint) $this->module->setError(422, '_checkpoint', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Line $line */
        $line = Line::find()->andWhere([
            'AND',
            ['=', 'driver_id', $user->id],
            ['=', 'status', Line::STATUS_IN_PROGRESS]
        ])->one();
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $log = new RestFul([
            'type' => RestFul::TYPE_DRIVER_CHECKPOINT_ARRIVE,
            'message' => json_encode(['status' => 'passed', 'checkpoint' => $checkpoint->id, 'line' => $line->id]),
            'user_id' => $user->id,
            'uip' => $_SERVER['REMOTE_ADDR']
        ]);
        $log->save();

        // TODO: Notification прибытие на точку

        $this->module->data['amount'] = [];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCalculateTariff()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['startpoint', 'endpoint']);

        /** @var \app\models\Checkpoint $startpoint */
        $startpoint = Checkpoint::findOne($this->body->startpoint);
        if (!$startpoint || $startpoint->type != $startpoint::TYPE_START) $this->module->setError(422, '_startpoint', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Checkpoint $endpoint */
        $endpoint = Checkpoint::findOne($this->body->endpoint);
        if (!$endpoint || $endpoint->type != $endpoint::TYPE_END) $this->module->setError(422, '_endpoint', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Route $route */
        $route = Route::findOne($startpoint->route);
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        // TODO: Переделать логику
        $rate = $this->getRate($route->id);
        $seat = (float) round($route->base_tariff * $rate, 2);

        $commission = (float) 0.0;

        $this->module->data['amount'] = [
            'base_tariff'   => $route->base_tariff,
            'rate'          => $rate,
            'seat'          => $seat,
            'commission'    => $commission
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionOnLine($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['vehicle_id', 'startpoint', 'endpoint', 'seats', 'freeseats']);

        /** @var \app\models\Route $route */
        $route = Route::findOne($id);
        if (!$route) $this->module->setError(422, '_route', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\modules\api\models\Vehicles $vehicle */
        $vehicle = Vehicles::findOne($this->body->vehicle_id);
        if (!$vehicle) $this->module->setError(422, '_vehicle', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Checkpoint $startpoint */
        $startpoint = Checkpoint::findOne($this->body->startpoint);
        if (!$startpoint || $startpoint->type != $startpoint::TYPE_START) $this->module->setError(422, '_startpoint', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Checkpoint $endpoint */
        $endpoint = Checkpoint::findOne($this->body->endpoint);
        if (!$endpoint || $endpoint->type != $endpoint::TYPE_END) $this->module->setError(422, '_endpoint', Yii::$app->mv->gt("Не найден", [], false));

        $seats = isset ($this->body->seats) ? intval($this->body->seats) : $vehicle->seats;
        if ($seats == 0) $seats = $vehicle->seats;

        $freeseats = isset ($this->body->freeseats) ? intval($this->body->freeseats) : $vehicle->seats;
        if ($freeseats == 0) $freeseats = $vehicle->seats;

        /** @var \app\models\Line $line */
        $line = new Line();
        $line->status = Line::STATUS_QUEUE;
        $line->driver_id = $user->id;
        $line->vehicle_id = $vehicle->id;
        $line->tariff = $route->base_tariff;
        $line->route_id = $route->id;
        $line->seats = $seats;
        $line->freeseats = $freeseats;
        $line->startpoint = $startpoint->id;
        $line->endpoint = $endpoint->id;

        if (!$line->validate() || !$line->save())
        {
            if ($line->hasErrors())
            {
                foreach ($line->errors as $field => $error_message)
                    $this->module->setError(422, 'line.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, '_line', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data['line'] = $line->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionStartpoints()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $points = [];

        $startpoints = Checkpoint::find()->where(['type' => Checkpoint::TYPE_START])->all();
        if ($startpoints && count($startpoints) > 0) foreach ($startpoints as $point)
        {
            /** @var $point \app\models\Checkpoint */
            $points[] = $point->toArray();
        }

        $this->module->data = $points;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionStartpointsRoute($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $points = [];

        $startpoints = Checkpoint::find()->andWhere([
            'AND',
            ['=', 'type', Checkpoint::TYPE_START],
            ['=', 'route', $id]
        ])->all();
        if ($startpoints && count($startpoints) > 0) foreach ($startpoints as $point)
        {
            /** @var $point \app\models\Checkpoint */
            $points[] = $point->toArray();
        }

        $this->module->data = $points;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionEndpoints()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $points = [];

        $endpoints = Checkpoint::find()->where(['type' => Checkpoint::TYPE_END])->all();
        if ($endpoints && count($endpoints) > 0) foreach ($endpoints as $point)
        {
            /** @var $point \app\models\Checkpoint */
            $points[] = $point->toArray();
        }

        $this->module->data = $points;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionEndpointsRoute($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $points = [];

        $endpoints = Checkpoint::find()->andWhere([
            'AND',
            ['=', 'type', Checkpoint::TYPE_END],
            ['=', 'route', $id]
        ])->all();
        if ($endpoints && count($endpoints) > 0) foreach ($endpoints as $point)
        {
            /** @var $point \app\models\Checkpoint */
            $points[] = $point->toArray();
        }

        $this->module->data = $points;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCheckpoints()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $points = [];

        $checkpoints = Checkpoint::find()->where(['type' => Checkpoint::TYPE_STOP])->all();
        if ($checkpoints && count($checkpoints) > 0) foreach ($checkpoints as $point)
        {
            /** @var $point \app\models\Checkpoint */
            $points[] = $point->toArray();
        }

        $this->module->data = $points;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCheckpointsRoute($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $points = [];

        $checkpoints = Checkpoint::find()->andWhere([
            'AND',
            ['=', 'type', Checkpoint::TYPE_STOP],
            ['=', 'route', $id]
        ])->all();
        if ($checkpoints && count($checkpoints) > 0) foreach ($checkpoints as $point)
        {
            /** @var $point \app\models\Checkpoint */
            $points[] = $point->toArray();
        }

        $this->module->data = $points;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionSeats($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        /** @var \app\models\Line $line */
        $lines = Line::find()->andWhere(['AND', ['=', 'route_id', $id]])->all();
        if (!$lines) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $seats = 0;
        $free_seats = 0;

        foreach ($lines as $line)
        {
            $seats += $line->seats;
            $free_seats += $line->freeseats;
        }

        $this->module->data['seats'] = $seats;
        $this->module->data['free_seats'] = $free_seats;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionPassengers($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $passengers = Trip::find()->where(['route_id' => $id])->all();
        if (!$passengers) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $_passengers = [];
        /** @var \app\models\Trip $passenger */
        foreach ($passengers as $passenger) $_passengers[] = $passenger->toArray();

        $this->module->data['passengers'] = $_passengers;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUpdateLine($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $line = $this->getLine($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найдена", [], false));

        $this->prepareBody();
        $this->validateBodyParams(['freeseats', 'seats']);

        if ($line->cancel_reason == 0) $line->cancel_reason = '';

        $line->freeseats = intval($this->body->freeseats);
        $line->seats = intval($this->body->seats);

        if (!$line->validate() || !$line->save())
        {
            if ($line->hasErrors())
            {
                foreach ($line->errors as $field => $error_message)
                    $this->module->setError(422, 'line.' . $field, Yii::$app->mv->gt($error_message[0], [], false), true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, '_line', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data['line'] = $line->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCancel($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['cancel_reason_trip', 'cancel_reason_line']);

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        $line->cancel_reason = $this->body->cancel_reason_line;
        $line->status = Line::STATUS_CANCELED;

        if (!$line->validate() || !$line->save())
        {
            if ($line->hasErrors())
            {
                foreach ($line->errors as $field => $error_message)
                    $this->module->setError(422, 'line.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, '_line', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        /** @var \app\models\Trip $trip */
        $trips = Trip::find()->andWhere(['route_id' => $line->route_id, 'vehicle_id' => $line->vehicle_id, 'driver_id' => $line->driver_id])->all();
        if ($trips)
        {
            $trip_errors = 0;
            $_trips = [];
            foreach ($trips as $trip)
            {
                $trip->cancel_reason = $this->body->cancel_reason_trip;
                $trip->status = Trip::STATUS_CANCELLED;

                if (!$trip->validate() || !$trip->save())
                {
                    if ($trip->hasErrors())
                    {
                        foreach ($trip->errors as $field => $error_message)
                        {
                            $this->module->setError(422, 'trip.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                            $trip_errors++;
                        }
                    }
                    else $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
                }

                $_trips[] = $trip->toArray();
            }

            if ($trip_errors > 0) $this->module->sendResponse();

            $this->module->data['trips'] = $_trips;
        }

        RestFul::updateAll([
            'message' => json_encode(['status' => 'cancel'])
        ], [
            'AND',
            ['=', 'user_id', $line->driver_id],
            ['=', 'type', RestFul::TYPE_DRIVER_ACCEPT]
        ]);

        $this->module->data['line'] = $line->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionPassengerDecline($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['passenger_id', 'cancel_reason_trip']);

        /** @var \app\models\Line $line */
        $line = Line::findOne($id);
        if (!$line) $this->module->setError(422, '_line', Yii::$app->mv->gt("Не найден", [], false));

        /** @var \app\models\Trip $trip */
        $trip = Trip::find()->where(['route_id' => $line->route_id, 'driver_id' => $line->driver_id, 'user_id' => $this->body->passenger_id])->one();
        if (!$trip) $this->module->setError(422, '_trip', Yii::$app->mv->gt("Не найден", [], false));

        $trip->cancel_reason = $this->body->cancel_reason_trip;
        $trip->status = Trip::STATUS_CANCELED;

        $this->module->data['trip'] = $trip->toArray();
        $this->module->data['line'] = $line->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    protected function getLine($line_id)
    {
        return Line::findOne($line_id);
    }

    protected function getRate($route_id)
    {
        /** @var \app\models\Line $line */
        $lines = Line::find()->andWhere([
            'AND',
            ['=', 'route_id', $route_id]
        ])->all();

        if (!$lines) return 1;

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
}
