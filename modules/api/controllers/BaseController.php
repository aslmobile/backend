<?php namespace app\modules\api\controllers;
use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;

use app\components\RestFul;
use app\modules\api\models\Devices;
use app\modules\api\models\Users;
use app\modules\api\models\UploadFiles;
use yii\web\ForbiddenHttpException;

use app\modules\api\models\RestFul as RestFulModel;

/** @property \app\modules\api\Module $module */
class BaseController extends RestFul
{
    public $modelClass = 'app\modules\api\models\RestFul';

    public $token;
    public $body;

    /** @var \app\modules\api\models\Users */
    public $user;

    /** @var \app\modules\api\models\Devices */
    public $device;

    const
        TOKEN = 0,
        TOKEN_PHONE = 1,
        TOKEN_SMS = 2;

    public function init()
    {
        parent::init();

        $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) $this->token = $matches[1];
        else $this->module->setError(403, '_token', "Token required!");
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

    public $scheme = false;
    public function prepareScheme($scheme = false)
    {
        if (!$scheme) foreach ($this->module->data as $data_scheme => $data)
            $this->loadScheme($data_scheme);
        else
            $this->loadScheme($scheme);
    }

    public function loadScheme($scheme)
    {
        $className = 'app\modules\api\models\\' . ucfirst($scheme);
        if (class_exists($className))
        {
            $model = new $className();
            $this->scheme = $model->scheme;
        }
    }

    protected function TokenAuth($type = self::TOKEN)
    {
        $device = Devices::findOne([
            'auth_token' => $this->token,
        ]);

        if ($device) $this->device = $device;

        switch ($type)
        {
            case self::TOKEN:
                if (!$this->device) $this->module->setError(403, '_device', "Not found");
                elseif (!isset ($this->device->auth_token) || empty($this->device->auth_token)) $this->authorizationTokenFailed('Device auth_token is empty!');

                $user = Users::findOne(['id' => $device->user_id]);
                if (!$user) $this->module->setError(403, '_user', "Not found");
                $this->user = $user;

                return Yii::$app->user->login($this->user, 0);
                break;

            case self::TOKEN_PHONE:
                $hash = strtoupper(hash('sha256', $this->body->phone . Yii::$app->params['salt']));
                if ($hash !== strtoupper($this->token)) $this->authorizationTokenFailed();
                break;

            case self::TOKEN_SMS:
                $this->prepareBody();
                $this->validateBodyParams(['code']);

                if (!$this->device) $this->module->setError(403, '_device', "Not found");
                elseif ($this->body->code != '000000' && $this->device->sms_code != $this->body->code) $this->module->setError(403, '_device', "Not found");
                elseif (!isset ($this->device->auth_token) || empty($this->device->auth_token)) $this->authorizationTokenFailed('Device auth_token is empty!');

                $this->device->sms_code = NULL;
                $this->device->save();

                $user = Users::findOne(['id' => $device->user_id]);
                if (!$user) $this->module->setError(403, '_user', "Not found");
                $this->user = $user;

                return Yii::$app->user->login($this->user, 0);
                break;
        }

        return true;
    }

    protected function authorizationTokenFailed($message = false)
    {
        $this->module->setError(403, 'Authorization Bearer', $message ? $message : "Token not valid!");
    }

    /**
     * @return Devices|array|bool|null|\yii\db\ActiveRecord
     */
    protected function Auth()
    {
        $device = false;
        if (isset ($this->body->device_id) && !empty($this->body->device_id))
            $device = Devices::findOne(['device_id' => $this->body->device_id]);

        if (!$device && !empty ($this->body->phone))
        {
            $user = Users::find()->where(['phone' => $this->body->phone])->one();
            if (!$user)
            {
                $user = new Users([
                    'phone' => $this->body->phone
                ]);

                if (!$user->save(false))
                {
                    $save_errors = $user->getErrors();
                    if ($save_errors && count ($save_errors) > 0)
                    {
                        foreach ($save_errors as $field => $error) $this->module->setError(422, $field, $error[0], true, false);
                        $this->module->sendResponse();
                    }
                    else $this->module->setError(422, '_user', "Problem with user creation");
                }
            }

            $this->user = $user;
            $device = new Devices([
                'user_id' => $user->id,
                'push_id' => $this->body->push_id,
                'lang' => $this->lang,
                'uip' => Yii::$app->request->userIP,
                'device_id' => $this->body->device_id,
                'type' => $this->body->ostype,
                'user_type' => $this->body->type
            ]);

            if (!$device->save())
            {
                $save_errors = $device->getErrors();
                if ($save_errors && count ($save_errors) > 0)
                {
                    foreach ($save_errors as $field => $error) $this->module->setError(422, $field, $error[0], true, false);
                    $this->module->sendResponse();
                }
                else $this->module->setError(422, '_device', "Problem with device creation");
            }
        }

        $this->device = $device;

        return $device;
    }

    protected function UploadFile($name, $path = 'photos', $return_file_id = false)
    {
        $_FILE = UploadedFile::getInstanceByName($name);

        $uploader = new UploadFiles();
        $path = '/files/' . $path;
        $path = $uploader->setPath($path);
        if ($path)
        {
            $uploader->uploadedFile = $_FILE;
            $_uploaded_file = $uploader->upload();

            if ($return_file_id) return $_uploaded_file;
            return $_uploaded_file['file'];
        }
        else $this->module->setError(411, '_path', "Can't create path");

        return false;
    }

    /**
     * Prepare request body
     */
    public function prepareBody()
    {
        $this->body = Yii::$app->request->getRawBody();
        if (empty ($this->body)) $this->body = @file_get_contents('php://input');

        $json = Yii::$app->request->get('json');
        if (!$json || $json != '1') $this->body = base64_decode($this->body);
        $this->body = json_decode($this->body, false, 1024);

        if (empty($this->body) && json_last_error() !== JSON_ERROR_NONE) $this->module->setError(422, '_json', json_last_error_msg());
        elseif (empty ($this->body)) $this->module->setError(422, '_body', 'Empty');

        $this->module->body = $this->body;
        $this->module->validateBody();
    }

    /**
     * @param bool|array|object $params
     */
    public function validateBodyParams($params = false)
    {
        if ($params && (is_array($params) || is_object($params))) foreach ($params as $param)
        {
            if (!isset ($this->body->$param) || empty ($this->body->$param))
                $this->module->setError(422, '_body.' . $param, 'Field required.');

            switch ($param)
            {
                case 'ostype':
                    if (!is_numeric($this->body->$param) || intval($this->body->$param) == 0)
                        $this->module->setError(422, '_body.' . $param, 'Numeric only.');
                    break;
            }
        }
        else $this->module->setError(422, '_body', 'Params has incorrect format');
    }

    public function logResponse($data = [])
    {
        $params = [
            'type'  => RestFulModel::TYPE_LOG,
            'message' => json_encode([
                'controller' => Yii::$app->controller->id,
                'action' => Yii::$app->controller->action->id,
                'url' => Url::current(),
                'time' => time(),
                'token' => $this->token,
                'response' => $data
            ]),
            'user_id' => Yii::$app->user->id ? Yii::$app->user->id : 0,
            'uip'   => Yii::$app->request->getUserIP()
        ];

        $logger = new RestFulModel($params);
        $logger->save();
    }

    public function logEvent()
    {
        $params = [
            'type'  => RestFulModel::TYPE_LOG,
            'message' => json_encode([
                'controller' => Yii::$app->controller->id,
                'action' => Yii::$app->controller->action->id,
                'url' => Url::current(),
                'time' => time(),
                'token' => $this->token
            ]),
            'user_id' => Yii::$app->user->id ? Yii::$app->user->id : 0,
            'uip'   => Yii::$app->request->getUserIP()
        ];

        $logger = new RestFulModel($params);
        $logger->save();
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        throw new ForbiddenHttpException("Access denied.", 403);
    }
}