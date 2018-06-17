<?php

namespace app\modules\admin\controllers;

use app\components\Controller;
use app\modules\admin\models\Gallery;
use app\modules\admin\models\GalleryContent;
use app\modules\admin\models\GalleryContentSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class GalleryContentController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete'],
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
                    if (($model = GalleryContent::findOne($v)) !== null) {
                        $model->delete();
                    }
                }

                return $this->redirect(['index']);
            }
        }

        $searchModel = new GalleryContentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GalleryContent model.
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
        $model = new GalleryContent();
        $model->scenario = 'create';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->created_by = Yii::$app->user->identity->getId();

            $transaction = Yii::$app->db->beginTransaction();

            $path = Yii::getAlias('@webroot') . '/files/galleries/' . $model->gallery->id;

            $file_content = UploadedFile::getInstance($model, 'path');
            $name = str_replace(" ", "_", $file_content->baseName) . '_' . time() . '.' . $file_content->extension;

            $image_extensions = explode(',', Yii::$app->params['image_extensions']);
            $video_extensions = explode(',', Yii::$app->params['video_extensions']);

            $folder = '';
            $type = 0;
            if (in_array($file_content->extension, $image_extensions)) {
                $type = 0;
                $folder = '/images/';
            } elseif (in_array($file_content->extension, $video_extensions)) {
                $type = 1;
                $folder = '/videos/';
            }

            if ($file_content->saveAs($path . $folder . $name)) {

                $model->type = $type;
                $model->path = '/files/galleries/' . $model->gallery->id . $folder . $name;
                $model->ext = $file_content->extension;

                if ($model->save()) {
                    if ($model->status == 2) {
                        $gallery_content = GalleryContent::find()->where(['status' => 2, 'gallery_id' => $model->gallery_id])->andWhere(['not', ['id' => $model->id]])->one();
                        if (!empty($gallery_content)) {
                            $gallery_content->status = 0;
                            $gallery_content->update(true, ['status']);
                        }
                    }
                    $transaction->commit();
                    Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Created', [], 0));
                } else {
                    FileHelper::unlink($path . $folder . $name);
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Error', [], 0));
                }
            } else {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Error', [], 0));
            }

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', ['model' => $model,]);
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
        $old_path = $model->path;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->updated_by = Yii::$app->user->identity->getId();

            $transaction = Yii::$app->db->beginTransaction();

            $path = Yii::getAlias('@webroot') . '/files/galleries/' . $model->gallery->id;

            $file_content = UploadedFile::getInstance($model, 'path');
            if (!empty($file_content)) {
                $name = str_replace(" ", "_", $file_content->baseName) . '_' . time() . '.' . $file_content->extension;

                $image_extensions = explode(',', Yii::$app->params['image_extensions']);
                $video_extensions = explode(',', Yii::$app->params['video_extensions']);

                $folder = '';
                $type = 0;
                if (in_array($file_content->extension, $image_extensions)) {
                    $type = 0;
                    $folder = '/images/';
                } elseif (in_array($file_content->extension, $video_extensions)) {
                    $type = 1;
                    $folder = '/videos/';
                }

                if ($file_content->saveAs($path . $folder . $name)) {

                    $model->type = $type;
                    $model->path = '/files/galleries/' . $model->gallery->id . $folder . $name;
                    $model->ext = $file_content->extension;
                } else {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Error', [], 0));
                }
            } else {
                $model->path = $old_path;
            }
            if ($model->save()) {
                if ($model->status == 2) {
                    $gallery_content = GalleryContent::find()->where(['status' => 2, 'gallery_id' => $model->gallery_id])->andWhere(['not', ['id' => $model->id]])->one();
                    if (!empty($gallery_content)) {
                        $gallery_content->status = 0;
                        $gallery_content->update(true, ['status']);
                    }
                }
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
     * Deletes an existing Order model.
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
            $model = GalleryContent::find()->where('id = :id', [':id' => $id])->multilingual()->one();
        } else {
            $model = GalleryContent::findOne($id);
        }

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::$app->mv->gt('Gallery not found', [], 0));
        }
    }
}
