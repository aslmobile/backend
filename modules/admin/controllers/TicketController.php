<?php

namespace app\modules\admin\controllers;

use app\components\Controller;
use app\components\paysystem\PaysystemInterface;
use app\components\paysystem\PaysystemProvider;
use app\models\PaymentCards;
use app\models\Transactions;
use app\modules\admin\models\Ticket;
use app\modules\admin\models\TicketSearch;
use app\modules\user\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view'],
                        'allow' => true,
                        'roles' => ['admin', 'moderator'],
                    ],
//                    [
//                        'actions' => ['delete', 'delete-group'],
//                        'allow' => true,
//                        'roles' => ['admin'],
//                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string|\yii\web\Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys)) {
                foreach ($keys as $k => $v) {
                    if (($model = Ticket::findOne($v)) !== null) {
                        $model->delete();
                    }
                }
                return $this->redirect(['index']);
            }
        }

        $searchModel = new TicketSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Ticket();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $ticket Ticket
     * @return \yii\web\Response
     * @throws \Exception
     */
    private function payOut($ticket)
    {
        $driver = User::findOne($ticket->user_id);
        if (empty($driver)) {
            Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Пользователь не найден', [], 0));
            return $this->redirect(['update', 'id' => $ticket->id]);
        }
        $card = PaymentCards::findOne($ticket->card_id);
        if (empty($card)) {
            Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Карта пользователя не найдена', [], 0));
            return $this->redirect(['update', 'id' => $ticket->id]);
        }
        if ($ticket->amount > $driver->balance) {
            Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('У пользователя не достаточно баланса', [], 0));
            return $this->redirect(['update', 'id' => $ticket->id]);
        }

        $transaction = new Transactions();
        $transaction->user_id = $driver->id;
        $transaction->recipient_id = $driver->id;
        $transaction->status = Transactions::STATUS_REQUEST;
        $transaction->amount = $ticket->amount;
        $transaction->gateway = Transactions::GATEWAY_OUT;
        $transaction->type = Transactions::TYPE_OUTCOME;
        $transaction->uip = Yii::$app->request->userIP;
        $transaction->currency = 'KZT';
        $transaction->route_id = 0;
        $transaction->trip_id = 0;
        $transaction->line_id = 0;
        $transaction->save();

        $data = ['driver' => \Yii::$app->params['use_pay']];
        $paysystem = PaysystemProvider::getDriver($data);

        if ($paysystem instanceof PaysystemInterface) {
            /** @var Transactions $transaction */
            $transaction = $paysystem->payOut($transaction, $card);
            if (!$transaction) {
                Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('У пользователя не достаточно баланса', [], 0));
                return $this->redirect(['update', 'id' => $ticket->id]);
            }
            $transaction->status = Transactions::STATUS_PAID;
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('У пользователя не достаточно баланса', [], 0));
            return $this->redirect(['update', 'id' => $ticket->id]);
        }

        return $this->redirect(['index']);

    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldStatus = $model->status;
        //if ($model->status == Ticket::STATUS_PAYED) return $this->redirect(['index']);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($oldStatus != Ticket::STATUS_PAYED && $model->status == Ticket::STATUS_PAYED) {
                $this->payOut($model);
            }

            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Saved', [], 0));
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
