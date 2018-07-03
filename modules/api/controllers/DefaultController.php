<?php namespace app\modules\api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/** @property \app\modules\api\Module $module */
class DefaultController extends BaseController
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
                            'dispatch-phone',

                            'method'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'dispatch-phone' => ['GET'],
                    'method' => ['POST']
                ]
            ]
        ];
    }

    public function actionDispatchPhone()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->module->data['phone'] = $this->coreSettings->phone;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionMethod()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $body = Yii::$app->request->rawBody;
        if (!isset($_GET['json']) || $_GET['json'] !== '1') $body = json_decode(base64_decode($body));
        else $body = json_decode($body);

        $this->module->data = $body;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }
}