<?php

namespace app\modules\admin\controllers;

use app\components\Controller;
use app\modules\admin\models\Translations;
use app\modules\admin\models\TranslationsSearch;
use app\modules\admin\models\Translation;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * TranslationsController implements the CRUD actions for Translations model.
 */
class TranslationsController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'edit'],
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
        ];
    }

    /**
     * Lists all Translations models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys)) {
                foreach ($keys as $k => $v) {
                    if (($model = Translations::findOne($v)) !== null) {
                        $model->delete();
                    }
                }

                return $this->redirect(['index']);
            }
        }

        $searchModel = new TranslationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Translations model.
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
     * Creates a new Translations model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Translations();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Translations model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Saved', [], 0));

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Translations model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionEdit()
    {

        $model = new Translation();

        $translations = Translation::getTranslations();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Saved', [], 0));
        }

        return $this->render('edit', [
            'model' => $model,
            'translations' => $translations,
        ]);
    }

    /**
     * Deletes an existing Translations model.
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
     * Finds the Translations model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Translations the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $ml = true)
    {
        if ($ml) {
            $model = Translations::find()->where('id = :id', [':id' => $id])->multilingual()->one();
        } else {
            $model = Translations::findOne($id);
        }

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
