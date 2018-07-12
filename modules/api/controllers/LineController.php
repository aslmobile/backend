<?php namespace app\modules\api\controllers;

use app\models\Checkpoint;
use app\models\Line;
use app\modules\api\models\Trip;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\modules\api\models\City;

/** @property \app\modules\api\Module $module */
class LineController extends BaseController
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
                            'startpoints', 'endpoints', 'checkpoints'
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
                ]
            ]
        ];
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

    public function actionSeats($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        /** @var \app\models\Line $line */
        $lines = Line::find()->andWhere(['AND', ['=', 'route_id', $id]])->all();
        if (!$lines) $this->module->setError(422, '_line', "Not Found");

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
        if (!$passengers) $this->module->setError(422, 'trip', "Not Found");

        $_passengers = [];
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
        if (!$line) $this->module->setError(422, '_line', "Not Found");

        $line->freeseats = 0;
        $line->seats = 0;

        $line->save();
    }

    protected function getLine($line_id)
    {
        return Line::findOne($line_id);
    }
}