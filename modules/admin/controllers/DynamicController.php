<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Dynamic;
use app\modules\admin\models\DynamicSearch;
use app\modules\admin\models\Lang;
use app\modules\admin\models\Metadata;
use app\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * DynamicController implements the CRUD actions for Dynamic model.
 */
class DynamicController extends Controller
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
     * Lists all Dynamic models.
     * @return mixed
     */
    public function actionIndex()
    {
		if (Yii::$app->request->isAjax) {
			$keys = (isset($_POST['keys']))?$_POST['keys']:[];
			if (count($keys)) {
				foreach ($keys as $k => $v) {
					if (($model = Dynamic::findOne($v)) !== null) {
						$model->delete();
					}
				}
				return $this->redirect(['index']);
			}
		}
		
        $searchModel = new DynamicSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Dynamic model.
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
     * Creates a new Dynamic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Dynamic();
        $meta = new Metadata();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($meta->load(Yii::$app->request->post())){
                $meta->data_id = $model->id;
                $meta->data_type = 0;
                $meta->save();
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'meta' => $meta,
            ]);
        }
    }

    /**
     * Updates an existing Dynamic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id, true);
        $meta =  Metadata::find()->where(['data_id'=>$id,'data_type'=>0])->multilingual()->one();
        if(!$meta){
            $meta = new Metadata();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($meta->load(Yii::$app->request->post())){
                $meta->data_id = $model->id;
                $meta->data_type = 0;
                $meta->save(false);
            }
			Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Saved',[],0));
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'meta' => $meta,
            ]);
        }
    }

    /**
     * Deletes an existing Dynamic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        $g = Metadata::find()->where(['data_id'=>$id,'data_type'=>0])->all();
        if($g){
            foreach ($g as $d){ $d->delete();}
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Dynamic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Dynamic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $ml = true)
    {
		if ($ml) {
			$model = Dynamic::find()->where('id = :id', [':id' => $id])->multilingual()->one();
		} else {
			$model = Dynamic::findOne($id);
		}
		
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::$app->mv->gt('Page not found',[],0));
        }
    }

    

}
