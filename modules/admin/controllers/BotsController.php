<?php namespace app\modules\admin\controllers;

use app\components\Controller;
use app\modules\admin\models\Bots;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class BotsController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'driver', 'passenger'],
                        'allow' => true,
                        'roles' => ['admin', 'moderator'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDriver()
    {
        $model = new Bots(['type' => Bots::TYPE_DRIVER]);
        return $this->render('driver', ['model' => $model]);
    }

    public function actionPassenger()
    {
        $model = new Bots(['type' => Bots::TYPE_PASSENGER]);
        return $this->render('passenger', ['model' => $model]);
    }
}
