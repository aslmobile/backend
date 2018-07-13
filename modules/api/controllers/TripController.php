<?php namespace app\modules\api\controllers;

use app\models\Line;
use app\models\Route;
use app\modules\api\models\Trip;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\modules\api\models\City;

/** @property \app\modules\api\Module $module */
class TripController extends BaseController
{
    public $modelClass = 'app\modules\api\models\RestFul';

    public function init()
    {
        parent::init();

        $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) $this->token = $matches[1];
        else $this->module->setError(403, '_token', "Token required!");
    }

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

                            'passenger-comments',
                            'driver-comments',
                            'trips',
                            'cancel'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'cities'  => ['GET'],
                    'cancel' => ['DELETE']
                ]
            ]
        ];
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

        $trips = [];
        $_trips = Trip::find()->where(['driver_id' => $user->id])->all();
        if ($_trips && count($_trips) > 0) foreach ($_trips as $trip) $trips[] = $trip->toArray();

        $this->module->data = $trips;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCancel()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['cancel_reason', 'route_id', 'vehicle_id']);

        $trip = Trip::findOne(['route_id' => $this->body->route_id, 'vehicle_id' => $this->body->vehicle_id]);
        if (!$trip) $this->module->setError(422, '_trip', "Not Found");

        $trip->cancel_reason = $this->body->cancel_reason;
        $trip->status = Trip::STATUS_CANCELED;

        if (!$trip->validate() || !$trip->save())
        {
            if ($trip->hasErrors())
            {
                foreach ($trip->errors as $field => $error_message)
                    $this->module->setError(422, 'trip.' . $field, $error_message, true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, 'trip', "Can't validate model from data.");
        }

        $this->module->data['trip'] = $trip->toArray();
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

    protected function getRate($route_id)
    {
        /** @var \app\models\Line $line */
        $lines = Line::find()->andWhere([
            'AND',
            ['=', 'route_id', $route_id]
        ])->all();

        if (!$lines) $this->module->setError(422, '_line', "Not Found");

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

    protected function calculatePassengerTariff($id)
    {
        $rate = $this->getRate($id);
        $taxi_tariff = 0;

        /** @var \app\models\Route $route */
        $route = Route::find()->where(['id' => $id])->one();
        if (!$route) $this->module->setError(422, '_route', "Not Found");

        $tariff = $route->base_tariff * $rate + $taxi_tariff;

        return [
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