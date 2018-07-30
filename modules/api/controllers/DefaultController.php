<?php namespace app\modules\api\controllers;

use app\components\Socket\SocketPusher;
use app\models\Answers;
use app\modules\api\models\Faq;
use app\modules\api\models\Legal;
use app\modules\api\models\UploadFiles;
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
                            'legal',
                            'faq',
                            'method',
                            'send-socket-message',
                            'cancel-trip-reasons',
                            'cancel-passenger-reasons',
                            'get-file'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'dispatch-phone' => ['GET'],
                    'legal' => ['GET'],
                    'cancel-trip-reasons' => ['GET'],
                    'cancel-passenger-reasons' => ['GET'],
                    'get-file' => ['GET'],
                    'method' => ['POST'],
                    'send-socket-message' => ['POST']
                ]
            ]
        ];
    }

    public function actionGetFile($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $file = UploadFiles::findOne($id);
        if (!$file) $this->module->setError(404, 'file', "Not Found");

        $this->module->data = [
            'file' => Yii::getAlias('@web') . $file->file,
            'created' => $file->created_at
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionDispatchPhone()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->module->data['phone'] = $this->coreSettings->phone;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionLegal($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        switch ($id)
        {
            case Legal::TYPE_DRIVER: $id = Legal::TYPE_DRIVER; break;
            case Legal::TYPE_PASSENGER: $id = Legal::TYPE_PASSENGER; break;
            default: $id = Legal::TYPE_DRIVER;
        }

        $legals = Legal::find()->where(['type' => $id])->all();
        $legal_data = [];

        /** @var \app\modules\api\models\Legal $legal */
        if ($legals && count($legals) > 0) foreach ($legals as $legal) $legal_data[] = $legal->toArray();

        $this->module->data = $legal_data;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionFaq()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $faqs = Faq::find()->orderBy(['weight' => SORT_ASC])->all();
        $faq_data = [];

        /** @var \app\modules\api\models\Faq $faq */
        if ($faqs && count($faqs) > 0) foreach ($faqs as $faq) $faq_data[] = $faq->toArray();

        $this->module->data = $faq_data;
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
    
    public function actionSendSocketMessage()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $message = [
            'action' => 'ping',
            'data' => [
                'message_id' => time(),
                'message' => 'pong'
            ]
        ];

        /** @var \app\components\Socket\SocketPusher $socket */
        $socket = new SocketPusher();
        if (!$socket->push(base64_encode(json_encode($message)))) $this->module->setError(422, 'socket.push', "Failure");

        $this->module->data = [
            'socket' => true,
            'message' => $message
        ];

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCancelTripReasons()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $answers = [];

        /** @var \app\models\Answers $answer */
        $_answers = Answers::find()->where(['type' => Answers::TYPE_CTR])->all();
        if ($_answers && count($_answers) > 0) foreach ($_answers as $answer) $answers[] = [
            'id' => $answer->id,
            'value' => $answer->answer
        ];

        if (empty ($answers) || count($answers) == 0) $answers = Yii::$app->params['cancel-passenger-reasons'];
        $this->module->data = $answers;

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCancelPassengerReasons()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $answers = [];

        /** @var \app\models\Answers $answer */
        $_answers = Answers::find()->where(['type' => Answers::TYPE_CPR])->all();
        if ($_answers && count($_answers) > 0) foreach ($_answers as $answer) $answers[] = [
            'id' => $answer->id,
            'value' => $answer->answer
        ];

        if (empty ($answers) || count($answers) == 0) $answers = Yii::$app->params['cancel-passenger-reasons'];
        $this->module->data = $answers;

        $this->module->setSuccess();
        $this->module->sendResponse();
    }
}