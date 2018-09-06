<?php namespace app\modules\api\controllers;

use app\components\Payments\PaymentProvider;
use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\components\RestFul;
use app\modules\api\models\RestFul as RestFulModel;

class PayboxController extends RestFul
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
                            'check', 'result', 'refund', 'capture', 'success', 'failure'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'check'     => ['POST'],
                    'result'    => ['POST'],
                    'refund'    => ['POST'],
                    'capture'   => ['POST'],
                    'success'   => ['POST'],
                    'failure'   => ['POST']
                ]
            ]
        ];
    }

    /**
     * @param \yii\base\Action $event
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($event)
    {
        $this->logEvent();

        return parent::beforeAction($event);
    }

    public function actionSuccess()
    {
        $paybox = PaymentProvider::getDriver([
            'driver' => 'PayBox'
        ]);
    }

    protected function logEvent()
    {
        $params = [
            'type'  => RestFulModel::TYPE_LOG,
            'message' => json_encode([
                'controller' => Yii::$app->controller->id,
                'action' => Yii::$app->controller->action->id,
                'url' => Url::current(),
                'time' => time(),
                'token' => false
            ]),
            'user_id' => Yii::$app->user->id ? Yii::$app->user->id : 0,
            'uip'   => Yii::$app->request->getUserIP()
        ];

        $logger = new RestFulModel($params);
        $logger->save();
    }
}