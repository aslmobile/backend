<?php

namespace app\modules\admin\controllers;

use app\components\Controller;
use app\models\Menu;
use app\modules\admin\models\MenuSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index',
                            'create',
                            'update',
                            'view'
                        ],
                        'allow' => true,
                        'roles' => [
                            'admin',
                            'moderator'
                        ],
                    ],
                    [
                        'actions' => [
                            'delete',
                            'delete-group'
                        ],
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
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex() {
        if(Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if(count($keys)) {
                foreach ($keys as $k => $v) {
                    if(($model = Menu::findOne($v)) !== null) {
                        $model->delete();
                    }
                }

                return $this->redirect(['index']);
            }
        }

        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Menu model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Menu();

        if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'index',
                'id' => $model->id
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Saved', [], 0));

            return $this->redirect([
                'update',
                'id' => $model->id
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $ml = true) {
        if($ml) {
            $model = Menu::find()->where('id = :id', [':id' => $id])->multilingual()->one();
        } else {
            $model = Menu::findOne($id);
        }

        if($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
