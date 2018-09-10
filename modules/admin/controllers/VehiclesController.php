<?php namespace app\modules\admin\controllers;

use app\modules\admin\models\Vehicles;
use app\modules\admin\models\VehiclesSearch;
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
                            'delete-type', 'delete-brand', 'delete-model',
                            'select-types', 'select-brands', 'select-models',
                            'select-vehicles'
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

        $searchModel = new VehiclesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Сохранено', [], 0));
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionCreate()
    {
        $model = new Vehicles();

        if ($model->load(Yii::$app->request->post()) && $model->save()) return $this->redirect(['index']);

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
            return $this->redirect(['brands']);

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
            return $this->redirect(['brands']);
        }

        return $this->render('brand', [
            'model' => $model,
        ]);
    }

    public function actionDeleteBrand($id)
    {
        $this->findBrandModel($id)->delete();

        return $this->redirect(['brands']);
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
            return $this->redirect(['models']);

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
            return $this->redirect(['models']);
        }

        return $this->render('model', [
            'model' => $model,
        ]);
    }

    public function actionDeleteModel($id)
    {
        $this->findModelModel($id)->delete();

        return $this->redirect(['models']);
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
            return $this->redirect(['types']);

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
            return $this->redirect(['types']);
        }

        return $this->render('type', [
            'model' => $model,
        ]);
    }

    public function actionDeleteType($id)
    {
        $this->findTypeModel($id)->delete();

        return $this->redirect(['types']);
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

    public function actionSelectVehicles($q = null, $id = null, $v = null)
    {
        if(!empty($id)) $id = explode(',',$id);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (is_null($q) && !is_null($v))
        {
            $out['results'] = Vehicles::find()
                ->select(['id', 'text' => 'license_plate'])
                ->andWhere([
                    ['user_id' => $v]
                ])
                ->andWhere([
                    'OR',
                    ['like', 'license_plate', $q]
                ])
                ->limit(10)->asArray()->all();
        }
        elseif (!is_null($q))
        {
            $out['results'] = Vehicles::find()
                ->select(['id', 'text' => 'license_plate'])
                ->andWhere([
                    'OR',
                    ['like', 'license_plate', $q]
                ])
                ->limit(10)->asArray()->all();
        }
        elseif (is_array($id))
        {
            $out['results'] = Vehicles::find()
                ->select(['id', 'text' => 'license_plate'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    protected function findTypeModel($id)
    {
        if (($model = VehicleType::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findBrandModel($id)
    {
        if (($model = VehicleBrand::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModelModel($id)
    {
        if (($model = VehicleModel::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModel($id)
    {
        if (($model = Vehicles::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
