<?php namespace app\modules\api;


use app\models\SmsTemplates;
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
        if (empty($this->body)) { $this->setError(401, '_body', Yii::$app->mv->gt("Тело запроса не должно быть пустым", [], false)); }
    }

    public function setError($code, $key, $message, $needheader = true, $immediatelyExit = true)
    {
        $this->status = 'error';
        $this->data = false;

        $this->error_code = $code;
        if ($needheader) Yii::$app->response->statusCode = 200;

        if (empty($this->error_message)) $this->error_message = new \StdClass();

        if (is_array($message) || is_object($message)) $message = implode(',', $message);
        $this->error_message->$key = $message;

        $immediatelyExit ? $this->sendResponse() : false;
    }

    public function setSuccess()
    {
        $this->status = 'success';
        $this->error_code = 0;
        $this->error_message = null;
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
     * @param $scheme array|object|bool
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
                    $this->setError(422, $data . '.' . $pointer, Yii::$app->mv->gt($validation_error->keyword(), [], false), true, false);

                    foreach ($validation_error->keywordArgs() as $param => $value)
                        $this->setError(422, $data . '.'. $pointer . '.' . $param, Yii::$app->mv->gt($value, [], false), true, false);
                }
            }

            $this->sendResponse();
        }
    }

    /**
     * @param $device \app\modules\api\models\Devices
     * @param $sandbox bool
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
                else $this->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));
            }

            if (!$sandbox)
            {
                if (!is_numeric($device->user->phone) || empty($device->user->phone) || intval($device->user->phone) == 0)
                    $this->setError(422, 'phone', Yii::$app->mv->gt("Не верный формат. " . $device->user->phone, [], false));

                /** @var \app\models\SmsTemplates $template */
                $template = SmsTemplates::find()->where(['name' => "auth-template"])->one();
                if ($template) $template = Yii::t('app', $template->template, ['name' => Yii::$app->params['defTitle'], 'code' => $code]);
                else $template = Yii::t('app','{name} | Авторизация: {code}', ['name' => Yii::$app->params['defTitle'], 'code' => $code]);

                $response = $SMSCenter->send($device->user->phone, $template, Yii::$app->params['smsc']['sender']);
                $send = new \SimpleXMLElement($response);
            }
            else $send = (object) ['cnt' => 1];
        }
        else $this->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));

        return isset ($send->cnt) && isset ($device) ? $device : false;
    }

    /**
     * @param $device \app\modules\api\models\Devices
     * @param $sandbox bool
     * @param $phone float
     * @return bool|Devices
     */
    public function verifyPhone($device, $phone, $sandbox = false)
    {
        if ($device && isset ($device->user->phone))
        {
            $SMSCenter = new SMSCenter(true, ['charset' => SMSCenter::CHARSET_UTF8, 'fmt' => SMSCenter::FMT_XML]);
            $code = (string) mt_rand(100000, 999999);

            $device->sms_code = $code;
            $device->save();

            if (!$sandbox)
            {
                if (!is_numeric($phone) || empty($phone) || intval($phone) == 0)
                    $this->setError(422, 'phone', Yii::$app->mv->gt("Не верный формат. " . $device->user->phone, [], false));

                /** @var \app\models\SmsTemplates $template */
                $template = SmsTemplates::find()->where(['name' => "verify-phone-template"])->one();
                if ($template) $template = Yii::t('app', $template->template, ['name' => Yii::$app->params['defTitle'], 'code' => $code]);
                else $template = Yii::t('app','{name} | Подтверждение: {code}', ['name' => Yii::$app->params['defTitle'], 'code' => $code]);

                $response = $SMSCenter->send($phone, $template, Yii::$app->params['smsc']['sender']);
                $send = new \SimpleXMLElement($response);
            }
            else $send = (object) ['cnt' => 1];
        }
        else $this->setError(422, '_device', Yii::$app->mv->gt("Не найден", [], false));

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

        if ((empty ($ret->data) && !$ret->data) || (is_array($ret->data) && count($ret->data) == 0)) $ret->data = null;

        if (!isset($_GET['json']) || $_GET['json'] !== '1') Yii::$app->response->content = base64_encode(json_encode($ret));
        else Yii::$app->response->content = json_encode($ret);

        Yii::$app->response->send();
        exit;
    }

    public function getNotificationsList()
    {
        /**
         * STATUS
         * - CREATED = 1
         * - DELIVERED = 2
         * - SEND = 3
         * - CANCELLED = 4
         *
         * TYPE
         * #1 - Водитель | Будьте готовы к выезду
         * #2 - Водитель | Настройки сохранены
         * #3 - Водитель | Встал на линию
         * #4 - Водитель | Пассажир сел в машину
         * #5 - Водитель | Отмена поездки
         * #6 - Водитель | Отмена посадки пассажира
         * #7 - Водитель | Подтвердил выезд
         * #8 - Водитель | Не подтвердил выезд
         * #9 - Водитель | Выехал
         * #10 - Водитель | Вам оставили отзыв
         * #11 - Водитель | Подтверждение машины
         *
         * #1 - Пассажир | Водитель выехал
         */
    }
}
