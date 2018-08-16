<?php namespace app\modules\api\controllers;

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
                            'transactions', 'transaction', 'cards', 'methods'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'transactions'  => ['POST'],
                    'transaction'  => ['GET'],
                    'methods'  => ['GET'],
                ]
            ]
        ];
    }

    public function actionTransactions()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['timestamp_min', 'timestamp_max']);

        $timestamp_min = intval($this->body->timestamp_min);
        $timestamp_max = intval($this->body->timestamp_max);

        $transactions = Transactions::find()->andWhere([
            'AND',
            ['between', 'created_at', $timestamp_min, $timestamp_max],
            ['=', 'user_id', $user->id]
        ])->orderBy(['created_at' => SORT_DESC])->all();

        $transactions_data = [];
        if ($transactions && count($transactions) > 0) foreach ($transactions as $transaction)
        {
            $transactions_data = $transaction->toArray();
        }

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
}