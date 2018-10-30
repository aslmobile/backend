<?php


namespace app\modules\main\controllers;


use app\components\Controller;
use app\components\paysystem\Drivers\PayBox;
use app\components\paysystem\Drivers\PayBoxSnappingCards;
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

        /** @var PayBox | PayBoxSnappingCards $controller */
        $controller = PaysystemProvider::getDriver($data);
        $controller->result = 1;

        if (method_exists($controller, 'updateTransaction')) return $controller->updateTransaction();

        return false;
    }

    public function actionResult($driver = '')
    {
        if (!$driver) $driver = \Yii::$app->request->post('driver');

        $data = ['driver' => $driver];

        /** @var PayBox | PayBoxSnappingCards $controller */
        $controller = PaysystemProvider::getDriver($data);

        if (method_exists($controller, 'updateTransaction')) {
            $response = $controller->updateTransaction();
            Yii::info(json_encode([
                'url' => 'actionResult',
                'ip' => Yii::$app->request->userIP,
                'headers' => Yii::$app->request->headers,
                'params' => ['response' => $response],
            ]), 'payment_info');
            if (isset($response['pg_status']) && $response['pg_status'] == 'ok') {
                $transaction = Transactions::findOne(intval(Yii::$app->request->post('pg_order_id', 0)));
                if (!empty($transaction) && $transaction->status == Transactions::STATUS_PAID) {
                    $recipient = User::findOne($transaction->recipient_id);
                    if (!empty($recipient)) {

                        if ($transaction->gateway == Transactions::GATEWAY_OUT)
                            $recipient->balance -= $transaction->amount;
                        else
                            $recipient->balance += $transaction->amount;

                        $recipient->save(false);
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

        /** @var PayBox | PayBoxSnappingCards $controller */
        $controller = PaysystemProvider::getDriver($data);

        if (method_exists($controller, 'callbackCard')) return $controller->callbackCard();

        return false;
    }

    public function actionSuccess()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return ['status' => 'success'];
    }

    public function actionFail()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        return ['status' => 'fail'];
    }
}
