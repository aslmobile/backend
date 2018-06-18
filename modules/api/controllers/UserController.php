<?php namespace app\modules\api\controllers;

use app\modules\api\models\Users;
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
                        'actions' => ['auth', 'sms', 'upload-driver-licence'],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'auth'  => ['POST'],
                    'sms'   => ['POST'],
                    'upload-driver-licence' => ['POST']
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
        $user = $this->TokenAuth(self::TOKEN_SMS);
        if ($user) $user = $this->user;

        $token = Yii::$app->security->generateRandomString();
        $this->module->data = [
            'user'   => $user->toArray(),
            'token' => $token
        ];

        $this->device->auth_token = $token;
        $this->device->save();

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadDriverLicence()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        $documents = [];
        foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'driver-licence/' . $user->id);

        echo '<pre>' . print_r($documents, true) . '</pre>';
        exit;
    }
}