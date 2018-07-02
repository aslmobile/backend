<?php namespace app\modules\api;


use app\modules\api\models\Devices;
use app\modules\api\models\Users;
use Yii;
use Opis\JsonSchema\{
    Validator,
    ValidationResult,
    ValidationError
};

use app\models\Lang;
use app\components\Sms\SMSCenter;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\api\controllers';

    public $allowedController = [];
    public $allowedMethod = [];

    public $body = [];
    public $status;
    public $error_code;
    public $lang;
    public $error_message = "";
    public $data = [];
    public $data_errors = [];

    public $token;
    public $salt;

    public function init()
    {
        $this->salt = Yii::$app->params['salt'];

        $headers = Yii::$app->request->headers;
        if ($headers->has('Accept-Language'))
        {
            $this->lang = $headers->get('Accept-Language');
            Lang::setCurrent($this->lang);
        }

        parent::init();
    }

    public function validateBody()
    {
        if (empty($this->body)) { $this->setError(401, '_body', 'Empty body'); }
    }

    public function setError($code, $key, $message, $needheader = true, $immediatelyExit = true)
    {
        $this->status = 'error';
        $this->data = false;

        $this->error_code = $code;
        if ($needheader) Yii::$app->response->statusCode = $code;

        if (empty($this->error_message)) $this->error_message = new \StdClass();
        $this->error_message->$key = $message;

        $immediatelyExit ? $this->sendResponse() : false;
    }

    public function setSuccess()
    {
        $this->status = 'success';
        $this->error_code = 0;
        $this->error_message = "no errors";
        Yii::$app->response->statusCode = 200;
    }

    public function replaceNullWithEmptyString($array)
    {
        foreach ($array as $key => $value)
        {
            if (is_array($value)) $array[$key] = $this->replaceNullWithEmptyString($value);
            elseif (is_null($value)) $array[$key] = "";
        }

        return $array;
    }

    /**
     * @param $data string
     * @param $scheme array|object
     */
    public function JSONValidate ($data, $scheme)
    {
        /** @var Validator $validator */
        $validator = new Validator();

        /** @var ValidationResult $result */
        $result = $validator->dataValidation((object) $this->data[$data], $scheme);
        if (!$result->isValid())
        {
            /** @var ValidationError $validation_error */
            $validation_errors = $result->getErrors();
            foreach ($validation_errors as $validation_error)
            {
                foreach ($validation_error->dataPointer() as $pointer)
                {
                    $this->setError(422, $data . '.' . $pointer, $validation_error->keyword(), true, false);

                    foreach ($validation_error->keywordArgs() as $param => $value)
                        $this->setError(422, $data . '.'. $pointer . '.' . $param, $value, true, false);
                }
            }

            $this->sendResponse();
        }
    }

    /**
     * @param $device \app\modules\api\models\Devices
     * @return bool|Devices
     * @throws \yii\base\Exception
     */
    public function sendSms($device, $sandbox = false)
    {
        if ($device && isset ($device->user->phone))
        {
            $SMSCenter = new SMSCenter(true, ['charset' => SMSCenter::CHARSET_UTF8, 'fmt' => SMSCenter::FMT_XML]);
            $code = (string) mt_rand(100000, 999999);

            $device->sms_code = $code;
            $device->auth_token = Yii::$app->security->generateRandomString();

            if (!$device->save())
            {
                $save_errors = $device->getErrors();
                if ($save_errors && count ($save_errors) > 0) foreach ($save_errors as $error)
                {
                    echo '<pre>' . print_r($error, true) . '</pre>';
                    exit;
                }
                else $this->setError(422, '_device', "Problem with device creation");
            }

            if (!$sandbox)
            {
                $response = $SMSCenter->send($device->user->phone, Yii::t('app','{app} | Авторизация: {code}', ['app' => Yii::$app->params['defTitle'], 'code' => $code]));
                $send = new \SimpleXMLElement($response);
            }
            else $send = (object) ['cnt' => 1];
        }

        return isset ($send->cnt) && isset ($device) ? $device : false;
    }

    public function sendResponse()
    {
        $ret = new \stdClass();
        $ret->status = $this->status;
        $ret->error_code = $this->error_code;
        $ret->error_message = $this->error_message;
        $ret->data = $this->data;

        if (is_array($ret->data)) $ret->data = $this->replaceNullWithEmptyString($ret->data);
        if (!empty($this->data_errors)) $ret->data_errors = $this->data_errors;

        if (empty ($ret->data) && !$ret->data) $ret->data = null;

        if (!isset($_GET['json']) || $_GET['json'] !== '1') Yii::$app->response->content = base64_encode(json_encode($ret));
        else Yii::$app->response->content = json_encode($ret);

        Yii::$app->response->send();
        exit;
    }
}
