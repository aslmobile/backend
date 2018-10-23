<?php


namespace app\modules\main\controllers;


use app\components\Controller;
use app\components\paysystem\PaysystemProvider;
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
        if (!$driver) {
            $driver = \Yii::$app->request->post('driver');
        }
        $data = ['driver' => $driver];

        $controller = PaysystemProvider::getDriver($data);

        if (method_exists($controller, 'updateTransaction')) {
            return $controller->checkTransaction();
        }

        return false;
    }

    public function actionResult($driver = '')
    {
        if (!$driver) {
            $driver = \Yii::$app->request->post('driver');
        }
        $data = ['driver' => $driver];

        $controller = PaysystemProvider::getDriver($data);

        if (method_exists($controller, 'updateTransaction')) {
            return $controller->updateTransaction();
        }

        return false;
    }

    public function actionCallback($driver = '')
    {
        if (!$driver) {
            $driver = \Yii::$app->request->post('driver');
        }
        $data = ['driver' => $driver];

        $controller = PaysystemProvider::getDriver($data);

        if (method_exists($controller, 'callbackCard')) {
            return $controller->callbackCard();
        }

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
