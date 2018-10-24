<?php namespace app\modules\api\controllers;

use app\components\paysystem\PaysystemProvider;
use app\components\paysystem\PaysystemSnappingCardsInterface;
use app\models\PaymentCards;
use app\models\Ticket;
use app\models\Transactions;
use app\models\User;
use app\modules\api\models\Trip;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\validators\NumberValidator;

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
                            'transactions', 'transaction', 'methods', 'in-out-methods', 'in-out-amounts',
                            'transactions-km',

                            'create-card', 'delete-card', 'cards', 'pay',
                            'ticket'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'transactions' => ['POST'],
                    'transactions-km' => ['POST'],
                    'transaction' => ['GET'],
                    'methods' => ['GET'],
                    'in-out-methods' => ['GET'],
                    'in-out-amounts' => ['POST'],

                    'create-card' => ['PUT'],
                    'pay' => ['PUT'],
                    'ticket' => ['PUT'],
                    'delete-card' => ['DELETE'],
                    'cards' => ['GET']
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
            'balance' => $user->balance
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
                    'transaction' => $transaction->toArray(),
                    'route' => ($transaction->route) ? $transaction->route->toArray() : null
                ];

        $this->module->data['transactions'] = $transactions_data;
        $this->module->data['count'] = Transactions::find()->andWhere([
            'AND',
            ['=', 'user_id', $user->id]
        ])->count();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTransactionsKm()
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
            ['=', 'user_id', $user->id],
            ['=', 'gateway', Transactions::GATEWAY_KM]
        ])->orderBy(['created_at' => SORT_DESC])->limit($limit)->offset($offset)->all();

        $transactions_data = [];
        if ($transactions && count($transactions) > 0)
            foreach ($transactions as $transaction)
                $transactions_data[] = [
                    'transaction' => $transaction->toArray(),
                    'route' => ($transaction->route) ? $transaction->route->toArray() : null
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

    public function actionInOutMethods()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->module->data = Transactions::getInOutMethods();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTicket()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['amount']);

        $validator = new NumberValidator();
        $validator->min = 1;

        if (!$validator->validate($this->body->amount)) $this->module->setError(422,
            '_amount', Yii::$app->mv->gt("Сумма должна быть не менее 1", [], false));

        $ticket = new Ticket();
        $ticket->user_id = $user->id;
        $ticket->status = Ticket::STATUS_NEW;
        $ticket->transaction_id = 0;
        $ticket->amount = floatval($this->body->amount);

        $ticket->save();

        $this->module->data['ticket'] = $ticket->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionPay($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['type']);

        $trip = Trip::findOne($id);
        if (!$trip) $this->module->setError(422,
            '_trip', Yii::$app->mv->gt("Не найдено", [], false));

        $trip->payment_status = Trip::PAYMENT_STATUS_WAITING;

        $validator = new NumberValidator();
        $validator->min = 1;
        $validator->integerOnly = true;

        $transaction = new Transactions();
        $transaction->user_id = $user->id;
        $transaction->status = Transactions::STATUS_REQUEST;
        $transaction->amount = $trip->amount;
        $transaction->gateway = Transactions::getGatewayServices()[$this->body->type];
        $transaction->uip = Yii::$app->request->userIP;
        $transaction->currency = $trip->currency;
        $transaction->route_id = $trip->route_id;
        if (!$transaction->validate() || !$transaction->save()) {
            if ($transaction->hasErrors()) {
                foreach ($transaction->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422, 'transaction.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                    $this->module->sendResponse();
                }
            } else $this->module->setError(422, '_transaction', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }


        if (isset($this->body->card) && $this->body->card) {

            if ($this->body->type != Transactions::GATEWAY_PAYBOX) $this->module->setError(422,
                '_type', Yii::$app->mv->gt("Тип оплаты не верен.", [], false));

            if (!$validator->validate($this->body->card)) $this->module->setError(422,
                '_card', Yii::$app->mv->gt("Карта не верна", [], false));

            $card = PaymentCards::findOne(['id' => $this->body->card, 'user_id' => $user->id]);
            if (!$card) $this->module->setError(422,
                '_card', Yii::$app->mv->gt("Не найдено", [], false));

            $data = ['driver' => \Yii::$app->params['use_paysystem']];
            $paysystem = PaysystemProvider::getDriver($data);

            if ($paysystem instanceof PaysystemSnappingCardsInterface) {

                $init_payment = $paysystem->initTransaction($transaction, $card);

                if (!$init_payment) $this->module->setError(422, '_payment',
                    Yii::$app->mv->gt("Во время инициализации платежа произошла ошибка!", [], false));

                $transaction = $paysystem->payTransaction($init_payment);
                if (!$transaction) $this->module->setError(422, '_payment',
                    Yii::$app->mv->gt("Во время проведения платежа произошла ошибка!", [], false));

                $trip->payment_status = Trip::PAYMENT_STATUS_PAID;

                /** @var User $driver */
                $driver = $trip->driver;
                if (!empty($driver)) {
                    $driver->balance += $transaction->amount;
                    $driver->save();
                }

            } else {
                $this->module->setError(422, '_card', Yii::$app->mv->gt("Осуществление платежа не доступно!", [], false));
            }

        }

        $trip->update();

        $this->module->data['trip'] = $trip->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCards()
    {

        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $data = ['driver' => \Yii::$app->params['use_paysystem']];
        $paysystem = PaysystemProvider::getDriver($data);

        if ($paysystem instanceof PaysystemSnappingCardsInterface) {
            $paysystem->getCardsList($user->id);
        } else {
            $this->module->setError(422, '_card', Yii::$app->mv->gt("Привязка карты не доступна!", [], false));
        }

        $this->module->data['user_id'] = $user->id;
        $this->module->data['cards'] = PaymentCards::getCards($user->id);
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCreateCard()
    {

        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $data = ['driver' => \Yii::$app->params['use_paysystem']];
        $paysystem = PaysystemProvider::getDriver($data);
        $result = '';

        if ($paysystem instanceof PaysystemSnappingCardsInterface) {
            $transaction = $paysystem->addCard($user->id);
            if (!empty($transaction->payment_link)) {
                $result = $transaction->payment_link;
            } else {
                $this->module->setError(422, '_card', Yii::$app->mv->gt("Платежная система не доступна", [], false));
            }
        } else {
            $this->module->setError(422, '_card', Yii::$app->mv->gt("Привязка карты не доступна!", [], false));
        }

        $this->module->data['user_id'] = $user->id;
        $this->module->data['iframe'] = $result;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionChangeMainCard($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $card = PaymentCards::findOne(['user_id' => $user->id, 'id' => $id]);

        if (!$card) $this->module->setError(422, '_card', Yii::$app->mv->gt("Не найдена", [], false));

        $card->status = PaymentCards::STATUS_MAIN;
        PaymentCards::updateAll(['status' => PaymentCards::STATUS_ACTIVE], ['user_id' => $user->id, 'status' => PaymentCards::STATUS_MAIN]);
        $card->save();

        $this->module->data['card'] = $card;
        $this->module->setSuccess();
        $this->module->sendResponse();

    }

    public function actionDeleteCard()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['cards']);

        $cards = PaymentCards::find()->where([
            'user_id' => $user->id,
            'status' => [PaymentCards::STATUS_ACTIVE, PaymentCards::STATUS_MAIN],
            'id' => $this->body->cards
        ])->all();

        if (empty($cards)) $this->module->setError(422, '_card', Yii::$app->mv->gt("Не найдены", [], false));

        $data = ['driver' => \Yii::$app->params['use_paysystem']];
        $paysystem = PaysystemProvider::getDriver($data);
        $deleted_cards = [];

        /** @var PaymentCards $card */
        foreach ($cards as $card) {
            if ($paysystem instanceof PaysystemSnappingCardsInterface) {

                if ($paysystem->deleteCard($card) && $card->delete()) $deleted_cards[] = [
                    'mask' => $card->getCardMask(),
                    'message' => Yii::$app->mv->gt("Карта {card} успешно удалена", ['card' => $card->getCardMask()], false)
                ];

            } else {
                $this->module->setError(422, '_card', Yii::$app->mv->gt("Удаление карты не доступно!", [], false));
            }
        }

        $this->module->data['cards'] = $deleted_cards;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }
}
