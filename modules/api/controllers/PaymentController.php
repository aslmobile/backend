<?php namespace app\modules\api\controllers;

use app\components\Payments\PaymentProvider;
use app\models\PaymentCards;
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

                            'create-card', 'delete-card', 'cards'
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

                    'create-card'       => ['PUT'],
                    'delete-card'       => ['DELETE'],
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
                $transactions_data[] = [
                    'transaction'   => $transaction->toArray(),
                    'route'         => ($transaction->route) ? $transaction->route->toArray() : null
                ];

        $this->module->data['transactions'] = $transactions_data;
        $this->module->data['count'] = Transactions::find()->andWhere([
            'AND',
            ['=', 'user_id', $user->id]
        ])->count();
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

    public function actionCards()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->module->data['user_id'] = $user->id;
        $this->module->data['cards'] = PaymentCards::getCards($user->id);
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionDeleteCard()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['cards']);

        $cards = PaymentCards::find()->andWhere([
            'AND',
            ['=', 'user_id', $user->id],
            ['=', 'status', PaymentCards::STATUS_ACTIVE],
            ['IN', 'id', $this->body->cards]
        ])->all();

        if (!$cards || count($cards) == 0) $this->module->setError(422, '_card', Yii::$app->mv->gt("Не найдены", [], false));

        /** @var \app\models\PaymentCards $card */
        $deleted_cards = [];
        foreach ($cards as $card)
        {
            if ($card->delete()) $deleted_cards[] = [
                'mask' => $card->getCardMask(),
                'message' => Yii::$app->mv->gt("Карта {card} успешно удалена", ['card' => $card->getCardMask()], false)
            ];
        }

        $this->module->data['cards'] = $deleted_cards;
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

        $card = new PaymentCards();
        $card->pg_card_id = uniqid($user->id);
        $card->pg_card_hash = rand(100000,999999) . "-XX-XXXX-" . rand(1000,9999);
        $card->pg_merchant_id = Yii::$app->params['payments']['PayBox']['merchant_id'];
        $card->user_id = $user->id;
        $card->status = PaymentCards::STATUS_ACTIVE;
        $card->save();

        $this->module->data['user_id'] = $user->id;
        $this->module->data['card'] = $card->toArray();
        $this->module->data['iframe'] = $iframe_url ? $iframe_url : "https://paybox.kz/api/v2/cardstorage/view?pg_payment_id=2858b79d574a1ed9ca549adb6a102cdc";
        $this->module->setSuccess();
        $this->module->sendResponse();
    }
}