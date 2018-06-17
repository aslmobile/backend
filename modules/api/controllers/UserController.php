<?php namespace app\modules\api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/** @property \app\modules\api\Module $module */
class UserController extends BaseController
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
                        'actions' => ['auth', 'sms'],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'auth'  => ['POST'],
                    'sms'   => ['POST']
                ]
            ]
        ];
    }

    public function actionAuth()
    {
        $this->TokenAuth(self::TOKEN_PHONE);
        $this->prepareBody();
        $this->validateBodyParams(['push_id', 'device_id', 'ostype', 'type', 'phone']);

        /** @var \app\modules\api\models\Devices $device */
        $device = $this->Auth();
        $device = $this->module->sendSms($device, true);

        if ($device)
        {
            $this->module->data = [
                'sms' => 1,
                'user' => $device->user,
                'token' => $device->auth_token
            ];
            $this->module->setSuccess();
            $this->module->sendResponse();
        }
        else $this->module->setError(422, '_sms', "SMS not send");
    }

    public function actionSms()
    {
        $this->TokenAuth(self::TOKEN_SMS);

        Yii::$app->user->login($this->device->user, 3600*24*30);
        if (!Yii::$app->user->isGuest)
        {
            $token = Yii::$app->security->generateRandomString();
            $this->module->data = [
                'user'   => Yii::$app->user->identity->toArray(),
                'token' => $token
            ];

            $this->device->auth_token = $token;
            $this->device->save();

            $this->module->setSuccess();
            $this->module->sendResponse();
        }
        else $this->module->setError(422, '_user', "Authorization Failed");
    }
}