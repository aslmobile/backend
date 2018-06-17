<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Metadata;
use app\modules\admin\models\MetadataSearch;
use app\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * MetadataController implements the CRUD actions for Metadata model.
 */
class MetadataController extends Controller
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
     * Lists all Metadata models.
     * @return mixed
     */
    public function actionIndex()
    {
		if (Yii::$app->request->isAjax) {
			$keys = (isset($_POST['keys']))?$_POST['keys']:[];
			if (count($keys)) {
				foreach ($keys as $k => $v) {
					if (($model = Metadata::findOne($v)) !== null) {
						$model->delete();
					}
				}
				return $this->redirect(['index']);
			}
		}
		
        $searchModel = new MetadataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Metadata model.
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
     * Creates a new Metadata model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Metadata();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Metadata model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->getSession()->setFlash('success', 'Saved');
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Metadata model.
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
    * Finds the Metadata model based on its primary key value.
    * If the model is not found, a 404 HTTP exception will be thrown.
    * @param integer $id
    * @return Metadata the loaded model
    * @throws NotFoundHttpException if the model cannot be found
    */
protected function findModel($id, $ml = true)
        {
        if ($ml) {
            $model =  Metadata::find()->where('id = :id', [':id' => $id])->multilingual()->one();
        } else {
            $model =  Metadata::findOne($id);
        }

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
