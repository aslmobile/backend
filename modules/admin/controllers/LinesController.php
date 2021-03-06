<?php namespace app\modules\admin\controllers;

use app\components\Controller;
use app\modules\admin\models\Checkpoint;
use app\modules\admin\models\CheckpointSearch;
use app\modules\admin\models\Line;
use app\modules\admin\models\LineSearch;
use app\modules\admin\models\LineSearchVehicles;
use app\modules\admin\models\Route;
use app\modules\admin\models\RouteSearch;
use app\modules\admin\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class LinesController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'online',
                            'index', 'create', 'update', 'view',
                            'routes', 'create-route', 'update-route', 'view-route',
                            'checkpoints', 'create-checkpoint', 'update-checkpoint', 'view-checkpoint',

                            'select-route', 'select-startpoints', 'select-endpoints',

                            'vehicles-queue', 'vehicles-ready', 'vehicles-trip',

                            'children'
                        ],
                        'allow' => true,
                        'roles' => ['admin', 'moderator'],
                    ],
                    [
                        'actions' => ['delete', 'delete-route'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
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

    public function actionVehiclesQueue()
    {
        $searchModel = new LineSearchVehicles();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Line::STATUS_QUEUE);

        return $this->render('vehicles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionVehiclesReady()
    {
        $searchModel = new LineSearchVehicles();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Line::STATUS_WAITING);

        return $this->render('vehicles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionVehiclesTrip()
    {
        $searchModel = new LineSearchVehicles();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Line::STATUS_IN_PROGRESS);

        return $this->render('vehicles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new LineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Line();

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view', 'id' => $model->id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findLineModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Сохранено', [], 0));
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findLineModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionRoutes()
    {
        if (Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys)) {
                foreach ($keys as $k => $v) if (($model = Route::findOne($v)) !== null) $model->delete();
                return $this->redirect(['routes']);
            }
        }

        $searchModel = new RouteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('routes', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateRoute()
    {
        $model = new Route();

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view-route', 'id' => $model->id]);

        return $this->render('create/route', [
            'model' => $model,
        ]);
    }

    public function actionViewRoute($id)
    {
        return $this->render('view/route', [
            'model' => $this->findRouteModel($id),
        ]);
    }

    public function actionUpdateRoute($id)
    {
        $model = $this->findRouteModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Сохранено', [], 0));
            return $this->redirect(['update-route', 'id' => $model->id]);
        } else {
            return $this->render('update/route', [
                'model' => $model,
            ]);
        }
    }

    public function actionDeleteRoute($id)
    {
        $this->findRouteModel($id)->delete();
        return $this->redirect(['routes']);
    }

    public function actionCheckpoints()
    {
        if (Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys)) {
                foreach ($keys as $k => $v) if (($model = Checkpoint::findOne($v)) !== null) $model->delete();
                return $this->redirect(['checkpoints']);
            }
        }

        $searchModel = new CheckpointSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('checkpoints', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateCheckpoint()
    {
        $model = new Checkpoint();

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['view-checkpoint', 'id' => $model->id]);

        return $this->render('create/checkpoint', [
            'model' => $model,
        ]);
    }

    public function actionUpdateCheckpoint($id)
    {
        $model = $this->findCheckpointModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Сохранено', [], 0));
            return $this->redirect(['update-checkpoint', 'id' => $model->id]);
        } else {
            return $this->render('update/checkpoint', [
                'model' => $model,
            ]);
        }
    }

    public function actionViewCheckpoint($id)
    {
        return $this->render('view/checkpoint', [
            'model' => $this->findCheckpointModel($id),
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findLineModel($id),
        ]);
    }

    public function actionSelectRoute($q = null, $id = null)
    {
        if (!empty($id)) $id = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $out['results'] = Route::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        } elseif (is_array($id)) {
            $out['results'] = Route::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    public function actionSelectStartpoints($q = null, $id = null)
    {
        if (!empty($id)) $id = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $out['results'] = Checkpoint::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'AND',
                    ['=', 'type', Checkpoint::TYPE_START]
                ])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        } elseif (is_array($id)) {
            $out['results'] = Checkpoint::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    public function actionSelectEndpoints($q = null, $id = null)
    {
        if (!empty($id)) $id = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $out['results'] = Checkpoint::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'AND',
                    ['=', 'type', Checkpoint::TYPE_END]
                ])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        } elseif (is_array($id)) {
            $out['results'] = Checkpoint::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    public function actionOnline(){
        $user_id = intval(Yii::$app->request->post('user_id', 0));
        $user = \app\models\User::findOne($user_id);
        $response = (!empty($user) && $user->online) ?
            '<span class="fa fa-circle text-green"></span> <small class="text-uppercase text-green">' . Yii::t('app', "В сети") . '</small>' :
            '<span class="fa fa-circle text-red"></span> <small class="text-uppercase text-red">' . Yii::t('app', "Оффлайн") . '</small>';
        return $response;
    }

    protected function findRouteModel($id)
    {
        if (($model = Route::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findCheckpointModel($id)
    {
        if (($model = Checkpoint::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findLineModel($id)
    {
        if (($model = Line::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $id
     * @return array
     */
    public function actionChildren($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return Checkpoint::getAllChildren(['route' => intval($id)]);
    }
}
