<?php namespace app\modules\api\controllers;

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
                            'passenger-comments',
                            'driver-comments',
                            'trips'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'cities'  => ['GET'],
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