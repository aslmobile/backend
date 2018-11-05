<?php

namespace app\modules\admin\controllers;

use app\components\Controller;
use app\models\Notifications;
use app\modules\admin\models\User;
use app\modules\admin\models\UserSearch;
use app\modules\api\models\UploadFiles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                            'index', 'create', 'update', 'view', 'select-users', 'select-drivers',
                            'passengers', 'drivers'
                        ],
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
//                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionPassengers()
    {

        if (Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys)) {
                foreach ($keys as $k => $v) {
                    if (($model = User::findOne($v)) !== null) {
                        $model->delete();
                    }
                }
                return $this->redirect(['index']);
            }
        }
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, User::TYPE_PASSENGER);

        return $this->render('passengers', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDrivers()
    {

        if (Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys)) {
                foreach ($keys as $k => $v) {
                    if (($model = User::findOne($v)) !== null) {
                        $model->delete();
                    }
                }
                return $this->redirect(['index']);
            }
        }

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, User::TYPE_DRIVER);

        return $this->render('drivers', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionIndex()
    {
        return $this->redirect(Url::toRoute(['/admin/user/passengers']));

        if (Yii::$app->request->isAjax) {
            $keys = (isset($_POST['keys'])) ? $_POST['keys'] : [];
            if (count($keys)) {
                foreach ($keys as $k => $v) {
                    if (($model = User::findOne($v)) !== null) {
                        $model->delete();
                    }
                }
                return $this->redirect(['index']);
            }
        }

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, [User::TYPE_ADMIN, User::TYPE_PASSENGER, 0]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return bool
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionEditColumn()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $pk = Yii::$app->request->post('pk');
        $name = Yii::$app->request->post('name');
        $value = Yii::$app->request->post('value');

        if (!empty($pk)) {
            $model = $this->findModel($pk);

            if (isset($model->$name)) {
                $model->$name = $value;

                if (!$model->validate() || !$model->save()) {
                    throw new HttpException('Failed saved!');
                }
            }
        } else {
            throw new HttpException('pk is empty!');
        }

        return true;
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
     * @return string|Response
     * @throws \Exception
     */
    public function actionCreate()
    {
        // Ukraine
        $model = new User(['country_id' => 2]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if (Yii::$app->request->post('roles') && Yii::$app->user->can('admin')) {
                $model->unlinkAll('roles', true);
                foreach (Yii::$app->request->post('roles') as $k => $v) {
                    // over rbac
                    $userRole = Yii::$app->authManager->getRole($v);
                    if (!empty($userRole)) {
                        Yii::$app->authManager->assign($userRole, $model->id);
                    }
                }
            }

            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }
        $oldStatus = $model->status;

        if ($model->load(Yii::$app->request->post())) {

            $uploaded = UploadedFile::getInstance($model, 'new_image');

            if (!is_null($uploaded)) {
                $path = '/files/user-photos/' . $model->id;

                if (self::validatePath(Yii::getAlias('@webroot') . $path)) {
                    $uploader = new UploadFiles();
                    $path = $uploader->setPath($path);
                    if ($path) {
                        $uploader->uploadedFile = $uploaded;
                        $file = $uploader->upload();

                        if ($file) {
                            $image = UploadFiles::findOne($model->image);
                            if ($image) $image->delete();

                            $model->image = $file['file_id'];
                        }
                    }
                }
            }

            if ($model->save()) {
                if (Yii::$app->request->post('roles') && Yii::$app->user->can('admin')) {
                    $model->unlinkAll('roles', true);
                    foreach (Yii::$app->request->post('roles') as $k => $v) {
                        // over rbac
                        $userRole = Yii::$app->authManager->getRole($v);
                        if (!empty($userRole)) {
                            Yii::$app->authManager->assign($userRole, $id);
                        }
                    }
                }

                if ($oldStatus != $model::STATUS_APPROVED && $model->status == $model::STATUS_APPROVED) {
                    $notifications = Notifications::create(Notifications::NTD_ACCEPTED, [$model->id]);
                    foreach ($notifications as $notification) Notifications::send($notification);
                }

                Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Сохранено', [], 0));
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::$app->mv->gt('Ошибка', [], 0));
                return $this->redirect(['update', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param null $q
     * @param null $id
     * @return array
     */
    public function actionSelectUsers($q = null, $id = null)
    {
        if (!empty($id)) $id = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $out['results'] = User::find()
                ->select(['id', 'text' => 'CONCAT(first_name,\' \',second_name)'])
                ->andWhere([
                    'OR',
                    ['like', 'first_name', $q],
                    ['like', 'second_name', $q],
                    ['like', 'email', $q]
                ])
                ->limit(10)->asArray()->all();
        } elseif (is_array($id)) {
            $out['results'] = User::find()
                ->select(['id', 'text' => 'CONCAT(first_name,\' \',second_name)'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    /**
     * @param null $q
     * @param null $id
     * @return array
     */
    public function actionSelectDrivers($q = null, $id = null)
    {
        if (!empty($id)) $id = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $out['results'] = User::find()
                ->select(['id', 'text' => 'CONCAT(first_name,\' \',second_name)'])
                ->andWhere([
                    'AND',
                    ['=', 'type', User::TYPE_DRIVER]
                ])
                ->andWhere([
                    'OR',
                    ['like', 'first_name', $q],
                    ['like', 'second_name', $q],
                    ['like', 'email', $q]
                ])->limit(10)->asArray()->all();
        } elseif (is_array($id)) {
            $out['results'] = User::find()
                ->select(['id', 'text' => 'CONCAT(first_name,\' \',second_name)'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $redirect = ['index'];

        $model = $this->findModel($id);
        if ($model) {
            if ($model->type == $model::TYPE_PASSENGER) $redirect = ['passengers'];
            elseif ($model->type == $model::TYPE_DRIVER) $redirect = ['drivers'];

            $model->delete();
        }

        return $this->redirect($redirect);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $path
     * @return bool
     */
    public static function validatePath($path)
    {
        if (!file_exists($path)
            && !@mkdir($path, 0777, true)
            && !is_dir($path)
        ) {
            $error = 'Can not create a folder to upload a file';
            return false;
        }

        return true;
    }
}
