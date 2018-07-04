<?php namespace app\modules\admin\controllers;

use app\modules\admin\models\Checkpoint;
use app\modules\admin\models\CheckpointSearch;
use app\modules\admin\models\LineSearch;
use app\modules\admin\models\Route;
use app\modules\admin\models\RouteSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\Controller;

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
                            'index', 'create', 'update', 'view',
                            'routes', 'create-route', 'update-route', 'view-route',
                            'checkpoints', 'create-checkpoint', 'update-checkpoint', 'view-checkpoint',

                            'select-route'
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

    public function actionIndex()
    {
        $searchModel = new LineSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRoutes()
    {
        if (Yii::$app->request->isAjax)
        {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys))
            {
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
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdateRoute($id)
    {
        $model = $this->findModel($id);

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
        $this->findModel($id)->delete();
        return $this->redirect(['routes']);
    }

    public function actionCheckpoints()
    {
        if (Yii::$app->request->isAjax)
        {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys))
            {
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

    public function actionSelectRoute($q = null, $id = null)
    {
        if (!empty($id)) $id = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q))
        {
            $out['results'] = Route::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        }
        elseif (is_array($id))
        {
            $out['results'] = Route::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    /**
     * Finds the VehicleType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Route the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Route::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}