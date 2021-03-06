<?php namespace app\modules\api\controllers;

use app\components\RestFul;
use app\modules\api\models\Devices;
use app\modules\api\models\RestFul as RestFulModel;
use app\modules\api\models\UploadFiles;
use app\modules\api\models\Users;
use app\modules\user\models\User;
use Yii;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

/** @property \app\modules\api\Module $module */
class BaseController extends RestFul
{
    public $modelClass = 'app\modules\api\models\RestFul';

    public $token;
    public $body;

    public $push_id;
    public $device_id;
    public $ostype;
    public $apptype;

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
        else $this->module->setError(403, '_token', Yii::$app->mv->gt("Токен является обязательным параметром", [], false));
    }

    /**
     * @param \yii\base\Action $event
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($event)
    {
        $this->logEvent();

        $push_id = Yii::$app->request->getHeaders()->get('push');
        $device_id = Yii::$app->request->getHeaders()->get('device');
        $ostype = Yii::$app->request->getHeaders()->get('ostype');
        $apptype = Yii::$app->request->getHeaders()->get('Application-Type');

        if (!is_object($this->body)) $this->body = new \StdClass();

        if ($push_id && !empty ($push_id)) $this->push_id = $push_id;
        else $this->module->setError(422, 'headers.push', Yii::$app->mv->gt("Обязательный параметр", [], false));

        if ($device_id && !empty ($device_id)) $this->device_id = $device_id;
        else $this->module->setError(422, 'headers.device', Yii::$app->mv->gt("Обязательный параметр", [], false));

        if ($ostype && !empty ($ostype)) $this->ostype = $ostype;
        else $this->module->setError(422, 'headers.ostype', Yii::$app->mv->gt("Обязательный параметр", [], false));

        if ($apptype && !empty($apptype) && $apptype) $this->apptype = $apptype;
        else $this->module->setError(422, 'headers.application-type', Yii::$app->mv->gt("Обязательный параметр", [], false));

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
        if (class_exists($className)) {
            $model = new $className();
            $this->scheme = $model->scheme;
        }
    }

    protected function TokenAuth($type = self::TOKEN)
    {
        $device = Devices::findOne(['auth_token' => $this->token]);

        if ($device) {
            $this->device = $device;
            $this->device->push_id = (string)$this->push_id;
            $this->device->device_id = (string)$this->device_id;
            $this->device->type = intval($this->ostype);
            $this->device->app = intval($this->apptype);
            $this->device->save();
        }

        switch ($type) {
            case self::TOKEN:

                if (!$this->device) $this->module->setError(403, 'session', Yii::$app->mv->gt("Не найден", [], false));
                elseif (!isset ($this->device->auth_token) || empty($this->device->auth_token)) $this->authorizationTokenFailed(Yii::$app->mv->gt("Токен не передан", [], false));

                $user = Users::findOne(['id' => $device->user_id, 'type' => $device->user_type]);
                if (!$user) $this->module->setError(403, '_user', Yii::$app->mv->gt("Не найден", [], false));
                if ($user->status == Users::STATUS_BLOCKED) $this->module->setError(403, '_user', Yii::$app->mv->gt("Вы в черном списке! Доступ ограничен.", [], false));
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

                if (!$this->device) $this->module->setError(403, 'session', Yii::$app->mv->gt("Не найден", [], false));
                elseif ($this->body->code != '000000' && $this->device->sms_code != $this->body->code) $this->module->setError(403, '_device', Yii::$app->mv->gt("Не найден", [], false));
                elseif (!isset ($this->device->auth_token) || empty($this->device->auth_token)) $this->authorizationTokenFailed(Yii::$app->mv->gt("Токен не передан", [], false));

                $this->device->sms_code = NULL;
                $this->device->save();

                $user = Users::findOne(['id' => $device->user_id, 'type' => $device->user_type]);
                if (!$user) $this->module->setError(403, '_user', Yii::$app->mv->gt("Не найден", [], false));
                if ($user->status == Users::STATUS_BLOCKED) $this->module->setError(403, '_user', Yii::$app->mv->gt("Вы в черном списке! Доступ ограничен.", [], false));
                $this->user = $user;

                return Yii::$app->user->login($this->user, 0);
                break;
        }

        return true;
    }

    protected function authorizationTokenFailed($message = false)
    {
        $this->module->setError(403, 'Authorization Bearer', $message ? $message : Yii::$app->mv->gt("Переданный токен имеет не верный формат", [], false));
    }

    /**
     * @return Devices|bool|null
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function Auth()
    {
        $device = false;
        if (isset ($this->device_id) && !empty($this->device_id))
            $device = Devices::findOne([
                'device_id' => $this->device_id,
                'user_type' => $this->body->type
            ]);

        if ($device && !$device->user) {
            if ($device) $device->delete();
            $device = false;
        } elseif ($device && $device->user->phone != $this->body->phone) {
            if ($device) $device->delete();
            $device = false;
        }

        if (!$device && !empty($this->body->phone) && !empty($this->body->type)) {
            $user = Users::find()->where(['phone' => $this->body->phone, 'type' => $this->body->type])->one();
            if (!$user) {
                $user = new Users([
                    'phone' => $this->body->phone,
                    'type' => $this->body->type
                ]);

                if (!$user->save(false)) {
                    $save_errors = $user->getErrors();
                    if ($save_errors && count($save_errors) > 0) {
                        foreach ($save_errors as $field => $error) $this->module->setError(422, $field, Yii::$app->mv->gt($error[0], [], false), true, false);
                        $this->module->sendResponse();
                    } else $this->module->setError(422, '_user', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
                }
            }

            $this->user = $user;
            $device = new Devices([
                'user_id' => $user->id,
                'push_id' => $this->push_id,
                'lang' => $this->lang,
                'uip' => Yii::$app->request->userIP,
                'device_id' => $this->device_id,
                'type' => $this->ostype,
                'user_type' => $this->body->type
            ]);

            if ($user->type != $this->body->type) {
                $user->type = $this->body->type;
                $user->save(false);
            }

            if ($user->type == User::TYPE_PASSENGER) {
                $user->approved = 1;
                $user->save();
            }

            if (!$device->save()) {
                $save_errors = $device->getErrors();
                if ($save_errors && count($save_errors) > 0) {
                    foreach ($save_errors as $field => $error) $this->module->setError(422, $field, Yii::$app->mv->gt($error[0], [], false), true, false);
                    $this->module->sendResponse();
                } else $this->module->setError(422, '_device', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
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

        if ($path) {

            $uploader->uploadedFile = $_FILE;
            $_uploaded_file = $uploader->upload();

            if ($return_file_id) {
                return $_uploaded_file;
            } else {
                return $_uploaded_file['file'];
            }

        } else $this->module->setError(411, '_path', Yii::$app->mv->gt("Не возможно создать директорию", [], false));

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
        elseif (empty ($this->body)) $this->module->setError(422, '_body', Yii::$app->mv->gt("Тело запроса не должно быть пустым", [], false));

        $this->module->body = $this->body;
        $this->module->validateBody();
    }

    /**
     * @param bool|array|object $params
     */
    public function validateBodyParams($params = false)
    {
        if ($params && (is_array($params) || is_object($params))) foreach ($params as $param) {
            if (!isset ($this->body->$param))
                $this->module->setError(422, '_body.' . $param, Yii::$app->mv->gt("Поле является обязательным параметром", [], false));

            if (empty ($this->body->$param) && $this->body->$param !== false && $this->body->$param !== 0)
                $this->module->setError(422, '_body.' . $param, Yii::$app->mv->gt("Поле не может быть пустым", [], false));

            switch ($param) {
                case 'ostype':
                    if (!is_numeric($this->body->$param) || intval($this->body->$param) == 0)
                        $this->module->setError(422, '_body.' . $param, Yii::$app->mv->gt("Поле должно быть в числовом формате", [], false));
                    break;
            }
        }
        else $this->module->setError(422, '_body', Yii::$app->mv->gt("Параметры имеют не верный формат", [], false));
    }

    public function logResponse($data = [])
    {
        $params = [
            'type' => RestFulModel::TYPE_LOG,
            'message' => json_encode([
                'controller' => Yii::$app->controller->id,
                'action' => Yii::$app->controller->action->id,
                'url' => Url::current(),
                'time' => time(),
                'token' => $this->token,
                'response' => $data
            ]),
            'user_id' => Yii::$app->user->id ? Yii::$app->user->id : 0,
            'uip' => Yii::$app->request->getUserIP()
        ];

        $logger = new RestFulModel($params);
        $logger->save();
    }

    public function logEvent()
    {
        $params = [
            'type' => RestFulModel::TYPE_LOG,
            'message' => json_encode([
                'controller' => Yii::$app->controller->id,
                'action' => Yii::$app->controller->action->id,
                'url' => Url::current(),
                'time' => time(),
                'token' => $this->token
            ]),
            'user_id' => Yii::$app->user->id ? Yii::$app->user->id : 0,
            'uip' => Yii::$app->request->getUserIP()
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
