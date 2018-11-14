<?php namespace app\modules\api\controllers;

use app\components\ArrayQuery\ArrayQuery;
use app\components\Socket\SocketPusher;
use app\models\Answers;
use app\models\Notifications;
use app\modules\admin\models\Agreement;
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
                            'agreement',
                            'faq',
                            'method',
                            'send-socket-message',
                            'cancel-trip-reasons',
                            'cancel-passenger-reasons',
                            'get-file',
                            'update-device',
                            'read-notification'
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
                    'agreement' => ['GET'],
                    'faq' => ['GET'],
                    'cancel-trip-reasons' => ['GET'],
                    'cancel-passenger-reasons' => ['GET'],
                    'get-file' => ['GET'],
                    'method' => ['POST'],
                    'send-socket-message' => ['POST'],
                    'update-device' => ['POST'],
                    'read-notification' => ['POST']
                ]
            ]
        ];
    }

    public function actionUpdateDevice()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionGetFile($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $file = UploadFiles::findOne($id);
        if (!$file) $this->module->setError(404, 'file', Yii::$app->mv->gt("Не найден", [], false));

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
        switch ($id) {
            case Legal::TYPE_DRIVER:
                $id = Legal::TYPE_DRIVER;
                break;
            case Legal::TYPE_PASSENGER:
                $id = Legal::TYPE_PASSENGER;
                break;
            default:
                $id = Legal::TYPE_DRIVER;
        }

        $legals = Legal::find()->where(['type' => $id])->orderBy(['weight' => SORT_ASC])->all();
        $legal_data = [];

        /** @var \app\modules\api\models\Legal $legal */
        foreach ($legals as $legal) {
            if (is_array($legal->content) && !empty($legal->content)) {
                $query = new ArrayQuery();
                $query->from($legal->content);
                $content = $query->orderBy(['weight' => SORT_ASC])->all();
                foreach ($content as $item){
                    $legal_data[] = [
                        'title' => $item->title,
                        'content' => $item->description
                    ];
                }
            }
        }

        $this->module->data = $legal_data;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionAgreement($id)
    {
        switch ($id) {
            case Agreement::TYPE_DRIVER:
                $id = Agreement::TYPE_DRIVER;
                break;
            case Agreement::TYPE_PASSENGER:
                $id = Agreement::TYPE_PASSENGER;
                break;
            default:
                $id = Agreement::TYPE_DRIVER;
        }

        $agreements = Agreement::find()->where(['type' => $id])->orderBy(['weight' => SORT_ASC])->all();
        $agreement_data = [];

        /** @var \app\modules\api\models\Agreement $agreement */
        foreach ($agreements as $agreement) {
            if (is_array($agreement->content) && !empty($agreement->content)) {
                $query = new ArrayQuery();
                $query->from($agreement->content);
                $content = $query->orderBy(['weight' => SORT_ASC])->all();
                foreach ($content as $item){
                    $agreement_data[] = [
                        'title' => $item->title,
                        'content' => $item->description
                    ];
                }
            }
        }

        $this->module->data = $agreement_data;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionFaq($id)
    {
        switch ($id) {
            case Faq::TYPE_DRIVER:
                $id = Faq::TYPE_DRIVER;
                break;
            case Faq::TYPE_PASSENGER:
                $id = Faq::TYPE_PASSENGER;
                break;
            default:
                $id = Faq::TYPE_DRIVER;
        }

        $faqs = Faq::find()->where(['type' => $id])->orderBy(['weight' => SORT_ASC])->all();
        $faq_data = [];

        /** @var \app\modules\api\models\Faq $faq */
        foreach ($faqs as $faq) {
            if (is_array($faq->content) && !empty($faq->content)) {
                $query = new ArrayQuery();
                $query->from($faq->content);
                $content = $query->orderBy(['weight' => SORT_ASC])->all();
                foreach ($content as $item){
                    $faq_data[] = [
                        'title' => $item->title,
                        'content' => $item->description
                    ];
                }
            }
        }

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
        if (!$socket->push(base64_encode(json_encode($message)))) $this->module->setError(422, 'socket.push', Yii::$app->mv->gt("Не удалось отправить сообщение", [], false));

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
        $_answers = Answers::findOne(['type' => Answers::TYPE_CTR]);
        if (is_array($_answers->answer) && !empty($_answers->answer)) {
            $query = new ArrayQuery();
            $query->from($_answers->answer);
            $content = $query->orderBy(['weight' => SORT_ASC])->all();
            foreach ($content as $answer) {
                $answers[] = [
                    'id' => $answer['id'],
                    'value' => $answer['answer'],
                ];
            }
        }

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
        $_answers = Answers::findOne(['type' => Answers::TYPE_CPR]);
        if (is_array($_answers->answer) && !empty($_answers->answer)) {
            $query = new ArrayQuery();
            $query->from($_answers->answer);
            $content = $query->orderBy(['weight' => SORT_ASC])->all();
            foreach ($content as $answer) {
                $answers[] = [
                    'id' => $answer['id'],
                    'value' => $answer['answer'],
                ];
            }
        }

        if (empty ($answers) || count($answers) == 0) $answers = Yii::$app->params['cancel-passenger-reasons'];
        $this->module->data = $answers;

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionReadNotification($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $notification = Notifications::findOne($id);
        if (!$notification) $this->module->setError(404, '_notification', Yii::$app->mv->gt("Не найден", [], false));

        $notification->status = Notifications::STATUS_DELIVERED;
        $notification->save();

        $this->module->data['notification'] = $notification->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }
}
