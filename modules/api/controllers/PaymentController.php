<?php namespace app\modules\api\controllers;

use app\components\Payments\PaymentProvider;
use app\models\Transactions;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\modules\api\models\City;

/** @property \app\modules\api\Module $module */
class PaymentController extends BaseController
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
                            'transactions', 'transaction', 'methods', 'in-out-amounts',

                            'create-card'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'transactions'      => ['POST'],
                    'transaction'       => ['GET'],
                    'methods'           => ['GET'],
                    'in-out-amounts'    => ['POST'],

                    'create-card'       => ['PUT']
                ]
            ]
        ];
    }

    public function actionInOutAmounts()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['timestamp_min', 'timestamp_max']);

        $timestamp_min = intval($this->body->timestamp_min);
        $timestamp_max = intval($this->body->timestamp_max);

        $income = Transactions::find()->andWhere([
            'AND',
            ['between', 'created_at', $timestamp_min, $timestamp_max],
            ['=', 'user_id', $user->id],
            ['=', 'type', Transactions::TYPE_INCOME],
        ])->sum('amount');

        $outcome = Transactions::find()->andWhere([
            'AND',
            ['between', 'created_at', $timestamp_min, $timestamp_max],
            ['=', 'user_id', $user->id],
            ['=', 'type', Transactions::TYPE_OUTCOME],
        ])->sum('amount');

        $this->module->data = [
            'income' => floatval($income),
            'outcome' => floatval($outcome),
            'balance'   => $user->balance
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTransactions()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['timestamp_min', 'timestamp_max']);

        $timestamp_min = intval($this->body->timestamp_min);
        $timestamp_max = intval($this->body->timestamp_max);

        $limit = isset ($this->body->limit) ? intval($this->body->limit) : 10;
        $offset = isset ($this->body->offset) ? intval($this->body->offset) : 0;

        $transactions = Transactions::find()->andWhere([
            'AND',
            ['between', 'created_at', $timestamp_min, $timestamp_max],
            ['=', 'user_id', $user->id]
        ])->orderBy(['created_at' => SORT_DESC])->limit($limit)->offset($offset)->all();

        $transactions_data = [];
        if ($transactions && count($transactions) > 0)
            foreach ($transactions as $transaction)
                $transactions_data = [
                    'transaction'   => $transaction->toArray(),
                    'route'         => ($transaction->route) ? $transaction->route->toArray() : null
                ];

        $this->module->data['transactions'] = $transactions_data;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTransaction($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $transaction = Transactions::findOne($id);
        if (!$transaction) $this->module->setError(422, 'transaction', Yii::$app->mv->gt("Не найдена", [], false));

        $this->module->data = $transaction->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionMethods()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->module->data = Transactions::getPaymentMethods();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCreateCard()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $paymentProvider = new PaymentProvider();

        /** @var \app\components\Payments\Drivers\PayBox $payBox */
        $payBox = $paymentProvider->getDriver(['driver' => 'PayBox']);
        $iframe_url = $payBox->addCard($user);

        $this->module->data['user'] = $user->toArray();
        $this->module->data['iframe'] = $iframe_url;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }
}