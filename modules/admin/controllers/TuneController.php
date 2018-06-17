<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Tune;
use app\modules\admin\models\TuneSearch;
use app\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * TuneController implements the CRUD actions for Tune model.
 */
class TuneController extends Controller
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
                        //'roles' => $this->module->controllerAccess(),
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
     * Lists all Tune models.
     * @return mixed
     */
    public function actionIndex()
    {
		if (Yii::$app->request->isAjax) {
			$keys = (isset($_POST['keys']))?$_POST['keys']:[];
			if (count($keys)) {
				foreach ($keys as $k => $v) {
					if (($model = Tune::findOne($v)) !== null) {
						$model->delete();
					}
				}
				return $this->redirect(['index']);
			}
		}
		
        $searchModel = new TuneSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tune model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Tune model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tune();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Tune model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(!$model){
            $model = new Tune([
                'id' => $id
            ]);
            $model->save();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->getSession()->setFlash('success', 'Изменения сохранены');
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Tune model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    /**
     * Finds the Tune model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tune the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $ml = true)
    {
        if ($ml) {
            $model = Tune::find()->where('id = :id', [':id' => $id])->multilingual()->one();
        } else {
            $model = Tune::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }
    }
}
