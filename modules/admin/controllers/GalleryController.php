<?php

namespace app\modules\admin\controllers;

use app\components\Controller;
use app\modules\admin\models\Gallery;
use app\modules\admin\models\GallerySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class GalleryController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'item'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Gallery models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys)) {
                foreach ($keys as $k => $v) {
                    if (($model = Gallery::findOne($v)) !== null) {
                        $model->delete();
                    }
                }

                return $this->redirect(['index']);
            }
        }

        $searchModel = new GallerySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Gallery model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Gallery model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Gallery();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->created_by = Yii::$app->user->identity->getId();

            $transaction = Yii::$app->db->beginTransaction();

            if ($model->save()) {

                $path = Yii::getAlias('@webroot') . '/files/galleries/' . $model->id;
                FileHelper::createDirectory($path . '/images');
                FileHelper::createDirectory($path . '/videos');

                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Created', [], 0));
            } else {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Error', [], 0));
            }

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Gallery model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * $model Item
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->updated_by = Yii::$app->user->identity->getId();

            $transaction = Yii::$app->db->beginTransaction();

            if ($model->save()) {

                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Saved', [], 0));
            } else {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Error', [], 0));
            }

            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }

    /**
     * Deletes an existing Gallery model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @param bool $ml
     *
     * @return null|static
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $ml = true)
    {
        if ($ml) {
            $model = Gallery::find()->where('id = :id', [':id' => $id])->multilingual()->one();
        } else {
            $model = Gallery::findOne($id);
        }

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::$app->mv->gt('Gallery not found', [], 0));
        }
    }
}
