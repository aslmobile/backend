<?php


namespace app\modules\main\controllers;


use app\components\Controller;
use app\components\paysystem\PaysystemProvider;
use app\models\Transactions;
use app\models\User;
use Yii;
use yii\web\Response;

class PaymentController extends Controller
{
    public $layout = '@app/views/layouts/empty';
    public $enableCsrfValidation = false;

    public function beforeAction($event)
    {
        $req = Yii::$app->request;

        Yii::info(json_encode([
            'url' => $event->actionMethod,
            'ip' => $req->getUserIP(),
            'headers' => $req->getHeaders(),
            'params' => ['get' => $req->get(), 'post' => $req->post(), 'body' => $req->getRawBody()],
        ]), 'payment_info');

        return parent::beforeAction($event);
    }

    public function actionCheck($driver = '')
    {
        if (!$driver) $driver = \Yii::$app->request->post('driver');

        $data = ['driver' => $driver];

        $controller = PaysystemProvider::getDriver($data);

        if (method_exists($controller, 'updateTransaction')) return $controller->checkTransaction();

        return false;
    }

    public function actionResult($driver = '')
    {
        if (!$driver) $driver = \Yii::$app->request->post('driver');

        $data = ['driver' => $driver];

        $controller = PaysystemProvider::getDriver($data);

        if (method_exists($controller, 'updateTransaction')) {
            $response = $controller->updateTransaction();
            if (isset($response['pg_status']) && $response['pg_status'] == 'ok') {
                $order_id = (isset($data['pg_order_id']) && !empty($data['pg_order_id']))
                    ? $data['pg_order_id'] : false;
                $payment_id = (isset($data['pg_payment_id']) && !empty($data['pg_payment_id']))
                    ? $data['pg_payment_id'] : false;
                if ($order_id || $payment_id) {
                    if ($order_id) $transaction = Transactions::findOne(intval($order_id));
                    else $transaction = Transactions::findOne(['payment_id' => intval($payment_id)]);
                    if (!empty($transaction) && $transaction->status == Transactions::STATUS_PAID) {
                        $recipient = User::findOne($transaction->recipient_id);
                        if (!empty($recipient)) {
                            $recipient->balance += $transaction->amount;
                            $recipient->save(false);
                        }
                    }
                }
            }
            return $response;
        }

        return false;
    }

    public function actionCallback($driver = '')
    {
        if (!$driver) $driver = \Yii::$app->request->post('driver');

        $data = ['driver' => $driver];

        $controller = PaysystemProvider::getDriver($data);

        if (method_exists($controller, 'callbackCard')) return $controller->callbackCard();

        return false;
    }

    public function actionSuccess()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return ['status' => 'success'];

        //return $this->render('success', []);
    }

    public function actionFail()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return ['status' => 'fail'];
        //return $this->render('fail', []);
    }
}
