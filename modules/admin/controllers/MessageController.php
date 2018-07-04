<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\SourceMessage;
use app\modules\admin\models\SourceMessageSearch;
use app\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\modules\admin\models\Lang;
use app\modules\admin\models\Message;

/**
 * MessageController implements the CRUD actions for SourceMessage model.
 */
class MessageController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update'],
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
     * Lists all SourceMessage models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys']))?$_POST['keys']:[];
            if (count($keys)) {
                foreach ($keys as $k => $v) {
                    if (($model = SourceMessage::findOne($v)) !== null) {
                        $model->delete();
                    }
                }
                return $this->redirect(['index']);
            }
        }

        $searchModel = new SourceMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new SourceMessage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SourceMessage();
        $translations = array();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->cache->flush();
            foreach ($_POST['message'] as $k => $v) {
                $translation_item = Message::find()->where('language = :language AND id = :id', [':language' => $k, ':id' => $model->id])->one();
                if ($translation_item === null) {
                    $translation_item = new Message;
                    $translation_item->id = $model->id;
                    $translation_item->language = $k;
                    $translation_item->translation = $v;
                    $translation_item->save();
                } else {
                    $translation_item->translation = $v;
                    $translation_item->save();
                }
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'translations' => $translations,
            ]);
        }
    }

    /**
     * Updates an existing SourceMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $translations = array();
        foreach (ArrayHelper::map(Lang::find()->where('lang.default = 0')->all(), 'local', 'flag') as $k => $v) {
            $translation_model = Message::find()->where('language = :language AND id = :id', [':language' => $k, ':id' => $id])->one();
            $translations[$k] = (!empty($translation_model->translation))?$translation_model->translation:'';
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->cache->flush();
            Yii::$app->getSession()->setFlash('success', 'Ð˜Ð·Ð¼ÐµÐ½ÐµÐ½Ð¸Ñ ÑÐ¾Ñ…Ñ€Ð°Ð½ÐµÐ½Ñ‹');
            foreach ($_POST['message'] as $k => $v) {
                $translation_item = Message::find()->where('language = :language AND id = :id', [':language' => $k, ':id' => $model->id])->one();
                if ($translation_item === null) {
                    $translation_item = new Message;
                    $translation_item->id = $model->id;
                    $translation_item->language = $k;
                    $translation_item->translation = $v;
                    $translation_item->save();
                } else {
                    $translation_item->translation = $v;
                    $translation_item->save();
                }
            }
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'translations' => $translations,
            ]);
        }
    }

    /**
     * Deletes an existing SourceMessage model.
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
     * Finds the SourceMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SourceMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SourceMessage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}