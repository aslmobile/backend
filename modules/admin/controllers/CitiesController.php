<?php

namespace app\modules\admin\controllers;


use app\components\Controller;
use app\modules\admin\models\City;
use app\modules\admin\models\CitySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * CitiesController implements the CRUD actions for City model.
 */
class CitiesController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view'],
                        'allow' => true,
                        'roles' => ['admin', 'moderator'],
                    ],
                    [
                        'actions' => ['delete', 'delete-group'],
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
                    if (($model = City::findOne($v)) !== null) {
                        $model->delete();
                    }
                }
                return $this->redirect(['index']);
            }
        }

        $searchModel = new CitySearch();
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
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new City();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt("Saved", [], 0));
            return $this->redirect(['update', 'id' => $model->id]);
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
     * @param $id
     * @param bool $ml
     * @return City|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $ml = true)
    {
        if ($ml) {
            $model = City::find()->where('id = :id', [':id' => $id])->multilingual()->one();
        } else {
            $model = City::findOne($id);
        }
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::$app->mv->gt('City not found', [], 0));
        }
    }
}
