<?php namespace app\modules\admin\controllers;

use app\components\Socket\SocketPusher;
use app\models\Checkpoint;
use app\models\Transactions;
use app\modules\admin\models\Line;
use app\modules\admin\models\User;
use app\modules\api\models\Route;
use app\modules\api\models\Trip;
use app\modules\api\models\Users;
use Yii;
use app\components\Controller;
use app\modules\admin\models\Bots;
use yii\db\Expression;
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

        if ($model->load(Yii::$app->request->post()))
        {
            switch ($model->action_type)
            {
                case 1:
                    $passengers = Users::find()->andWhere([
                        'AND',
                        ['=', 'type', Users::TYPE_PASSENGER],
                        ['=', 'status', Users::STATUS_APPROVED]
                    ])->all();

                    /** @var \app\models\Checkpoint $startpoint */
                    $startpoint = Checkpoint::find()->andWhere([
                        'AND',
                        ['=', 'type', Checkpoint::TYPE_START],
                        ['=', 'route', 5]
                    ])->one();

                    /** @var \app\models\Checkpoint $endpoint */
                    $endpoint = Checkpoint::find()->andWhere([
                        'AND',
                        ['=', 'type', Checkpoint::TYPE_END],
                        ['=', 'route', 5]
                    ])->one();

                    /** @var \app\modules\api\models\Users $passenger */
                    if ($passengers && $startpoint && $endpoint && count($passengers) > 0)
                    {
                        $passengers_count = [];

                        foreach ($passengers as $passenger)
                        {
                            $trip = new Trip();
                            $trip->status = Trip::STATUS_WAITING;
                            $trip->user_id = $passenger->id;
                            $trip->startpoint_id = $startpoint->id;
                            $trip->position = '48.4579235,35.026574';
                            $trip->seats = 1;
                            $trip->endpoint_id = $endpoint->id;
                            $trip->start_time = -1;
                            $trip->route_id = $startpoint->route;
                            $trip->amount = 100;
                            $trip->tariff = 100;
                            $trip->payment_type = Trip::PAYMENT_TYPE_CARD;
                            $trip->payment_status = Trip::PAYMENT_STATUS_PAID;
                            $trip->currency = 'T';

                            $trip->passenger_comment = 'БОТ';
                            $trip->passenger_description = 'БОТ';
                            $trip->passenger_rating = 5;
                            
                            $trip->driver_comment = 'БОТ';
                            $trip->driver_description = 'БОТ';
                            $trip->driver_rating = 5;

                            $trip->driver_id = 0;
                            $trip->vehicle_id = 0;
                            $trip->line_id = 0;

                            if ($trip->save()) $passengers_count[] = [
                                'trip_id' => $trip->id,
                                'passenger_id' => $trip->user_id
                            ];
                        }

                        Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Очередь успешно создана',[],0));
                        return $this->redirect(['/admin/trips/index']);
                    }

                    Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Не удалось создать очередь из за нехватки данных',[],0));
                    break;

                case 2:
                    /** @var \app\modules\api\models\Line $line */
                    $line = Line::find()->andWhere([
                        'AND',
                        ['=', 'driver_id', $model->driver_id],
                        ['=', 'status', Line::STATUS_QUEUE]
                    ])->one();

                    if ($line)
                    {
                        $trips = \app\models\Trip::find()->andWhere([
                            'AND',
                            ['=', 'route_id', $line->route_id],
                            ['=', 'status', Trip::STATUS_WAITING],
                            ['=', 'payment_status', Trip::PAYMENT_STATUS_PAID],
                            ['=', 'passenger_comment', 'БОТ']
                        ])->all();

                        /** @var \app\models\Trip $trip */
                        if ($trips && count($trips) > 0)
                        {
                            foreach ($trips as $trip)
                            {
                                $trip->driver_id = $line->driver_id;
                                $trip->vehicle_id = $line->vehicle_id;
                                $trip->vehicle_type_id = $line->vehicle->type->id;
                                $trip->line_id = $line->id;
                                $trip->status = Trip::STATUS_WAY;

                                if ($trip->save()) $passengers_count[] = [
                                    'trip_id' => $trip->id,
                                    'passenger_id' => $trip->user_id
                                ];
                            }

                            $line->status = Line::STATUS_WAITING;
                            if ($line->save())
                            {
                                Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Пассажиры успешно посаженны',[],0));

                                $socket = new SocketPusher();
                                $socket->push(base64_encode(json_encode([
                                    'action' => "acceptDriverTrip",
                                    'data' => [
                                        'message_id' => time()
                                    ]
                                ])));

                                return $this->redirect(['/admin/trips/index']);
                            }
                            else Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Не удалось сохранить информацию о поездке',[],0));
                        }
                        else Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Не удалось найти пассажиров',[],0));
                    }
                    else Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Не удалось найти линию',[],0));
                    break;
            }
        }

        return $this->render('passenger', ['model' => $model]);
    }
}
