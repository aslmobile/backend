<?php namespace app\modules\admin\controllers;

use app\models\Transactions;
use app\modules\admin\models\Line;
use app\modules\admin\models\User;
use app\modules\api\models\Trip;
use Yii;
use app\components\Controller;
use app\modules\admin\models\Bots;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;

class BotsController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'driver', 'passenger', 'transactions'],
                        'allow' => true,
                        'roles' => ['admin', 'moderator'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDriver()
    {
        $model = new Bots(['type' => Bots::TYPE_DRIVER]);

        if ($model->load(Yii::$app->request->post()))
        {
            $line = new Line([
                'status'        => $model->status,
                'driver_id'     => $model->driver_id,
                'vehicle_id'    => $model->vehicle_id,
                'tariff'        => 100,
                'route_id'      => $model->route_id,
                'startpoint'    => $model->start_point_id,
                'endpoint'      => $model->end_point_id,
                'freeseats'     => $model->status == Line::STATUS_FINISHED ? 0 : 1,
                'seats'         => 4,
                'starttime'     => time(),
                'endtime'       => $model->status == Line::STATUS_FINISHED ? time() + 3630 : 0
            ]);

            $line->save(false);

            $passengers = User::find()->andWhere([
                'AND',
                ['=', 'type', User::TYPE_PASSENGER],
                ['=', 'status', User::STATUS_APPROVED]
            ])->limit(4)->all();

            $trips = [];
            /** @var \app\models\User $passenger */
            if ($passengers && count($passengers) > 0) foreach ($passengers as $passenger)
            {
                $trip = new Trip([
                    'status'            => $model->status == Line::STATUS_FINISHED ? Trip::STATUS_FINISHED : Trip::STATUS_WAITING,
                    'user_id'           => $passenger->id,
                    'amount'            => $line->tariff,
                    'tariff'            => $line->tariff,
                    'currency'          => '₸',
                    'payment_type'      => Trip::PAYMENT_TYPE_CASH,
                    'payment_status'    => $model->status == Line::STATUS_FINISHED ? Trip::PAYMENT_STATUS_PAID : Trip::PAYMENT_STATUS_WAITING,
                    'startpoint_id'     => $line->startpoint,
                    'endpoint_id'       => $line->endpoint,
                    'route_id'          => $line->route_id,
                    'seats'             => 1,
                    'vehicle_type_id'   => $line->vehicle->type->id,
                    'line_id'           => $line->id,
                    'vehicle_id'        => $line->vehicle_id,
                    'driver_id'         => $line->driver_id
                ]);

                $trip->save(false);
                $trips[] = $trip;
            }

            return $this->redirect(Url::toRoute(['/admin/lines/view/' . $line->id . '/']));
        }

        return $this->render('driver', ['model' => $model]);
    }

    public function actionTransactions()
    {
        $model = new Bots(['type' => Bots::TYPE_USER]);

        if ($model->load(Yii::$app->request->post()))
        {
            $transaction = new Transactions([
                'status'    => $model->transaction_status,
                'user_id'   => $model->user_id,
                'amount'    => $model->transaction_amount,
                'gateway'   => $model->transaction_gateway,
                'uip'       => '0.0.0.0',
                'currency'  => '₸',
                'type'      => $model->transaction_type
            ]);

            $transaction->save(false);
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Транзакция успешно создана',[],0));
            $this->redirect(Url::toRoute(['/admin/user/view/' . $model->user_id . '/']));
        }

        return $this->render('transactions', ['model' => $model]);
    }

    public function actionPassenger()
    {
        $model = new Bots(['type' => Bots::TYPE_PASSENGER]);
        return $this->render('passenger', ['model' => $model]);
    }
}
