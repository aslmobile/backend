<?php namespace app\modules\api\controllers;

use app\models\Checkpoint;
use app\models\Line;
use app\models\Route;
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
        if ($user->type != Users::TYPE_DRIVER)
            $this->module->setError(403, 'user', Yii::$app->mv->gt("У пользователя нет прав на данное действие", [], false));

        return parent::beforeAction($event);
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
        $this->validateBodyParams(['vehicle_id', 'startpoint', 'endpoint']);

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

        /** @var \app\models\Line $line */
        $line = new Line();
        $line->driver_id = $user->id;
        $line->vehicle_id = $vehicle->id;
        $line->tariff = $route->base_tariff;
        $line->route_id = $route->id;
        $line->seats = $vehicle->seats;
        $line->freeseats = $vehicle->seats;
        $line->status = Line::STATUS_WAITING;
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
        foreach ($passengers as $passenger) $_passengers[] = $passenger->user->toArray();

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
                $trip->status = Trip::STATUS_CANCELED;

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
}