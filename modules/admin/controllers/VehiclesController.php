<?php namespace app\modules\admin\controllers;

use app\modules\admin\models\Vehicles;
use Yii;
use app\components\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

use app\modules\admin\models\VehicleBrand;
use app\modules\admin\models\VehicleModel;
use app\modules\admin\models\VehicleType;
use app\modules\admin\models\VehiclesBrandSearch;
use app\modules\admin\models\VehiclesModelSearch;
use app\modules\admin\models\VehiclesTypeSearch;

class VehiclesController extends Controller
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
                            'index', 'update', 'create', 'delete',
                            'types', 'brands', 'models',
                            'type', 'brand', 'model',
                            'create-type', 'create-brand', 'create-model',
                            'select-types', 'select-brands', 'select-models'
                        ],
                        'allow' => true,
                        'roles' => ['admin', 'moderator'],
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
        if (Yii::$app->request->isAjax)
        {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys))
            {
                foreach ($keys as $k => $v) if (($model = Vehicles::findOne($v)) !== null) $model->delete();
                return $this->redirect(['index']);
            }
        }

        $searchModel = new VehiclesBrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Vehicles();

        if ($model->load(Yii::$app->request->post()) && $model->save()) return $this->redirect(['update', 'id' => $model->id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionBrands()
    {
        if (Yii::$app->request->isAjax)
        {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys))
            {
                foreach ($keys as $k => $v) if (($model = VehicleBrand::findOne($v)) !== null) $model->delete();
                return $this->redirect(['brands']);
            }
        }

        $searchModel = new VehiclesBrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('brands', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateBrand()
    {
        $model = new VehicleBrand();

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['brand', 'id' => $model->id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionBrand($id)
    {
        $model = $this->findBrandModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Saved', [], 0));
            return $this->redirect(['brand', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionModels()
    {
        if (Yii::$app->request->isAjax)
        {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys))
            {
                foreach ($keys as $k => $v) if (($model = VehicleModel::findOne($v)) !== null) $model->delete();
                return $this->redirect(['models']);
            }
        }

        $searchModel = new VehiclesModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('models', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateModel()
    {
        $model = new VehicleModel();

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['model', 'id' => $model->id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionModel($id)
    {
        $model = $this->findModelModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Saved', [], 0));
            return $this->redirect(['model', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionTypes()
    {
        if (Yii::$app->request->isAjax)
        {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys))
            {
                foreach ($keys as $k => $v) if (($model = VehicleType::findOne($v)) !== null) $model->delete();
                return $this->redirect(['types']);
            }
        }

        $searchModel = new VehiclesTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('types', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateType()
    {
        $model = new VehicleType();

        if ($model->load(Yii::$app->request->post()) && $model->save())
            return $this->redirect(['type', 'id' => $model->id]);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionType($id)
    {
        $model = $this->findTypeModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Saved', [], 0));
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSelectTypes($q = null, $id = null)
    {
        if(!empty($id)) $id = explode(',',$id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q))
        {
            $out['results'] = VehicleType::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        }
        elseif (is_array($id))
        {
            $out['results'] = VehicleType::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    public function actionSelectBrands($q = null, $id = null)
    {
        if(!empty($id)) $id = explode(',',$id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q))
        {
            $out['results'] = VehicleBrand::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        }
        elseif (is_array($id))
        {
            $out['results'] = VehicleBrand::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    public function actionSelectModels($q = null, $id = null)
    {
        if(!empty($id)) $id = explode(',',$id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q))
        {
            $out['results'] = VehicleModel::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        }
        elseif (is_array($id))
        {
            $out['results'] = VehicleModel::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    /**
     * Finds the VehicleType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VehicleType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findTypeModel($id)
    {
        if (($model = VehicleType::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the VehicleType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VehicleBrand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findBrandModel($id)
    {
        if (($model = VehicleBrand::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the VehicleType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VehicleModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelModel($id)
    {
        if (($model = VehicleModel::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
