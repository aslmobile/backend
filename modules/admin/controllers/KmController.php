<?php namespace app\modules\admin\controllers;

use app\components\Controller;
use app\modules\admin\models\Km;
use app\modules\admin\models\Route;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class KmController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'settings'],
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

    public function actionSettings()
    {
        $model = Km::findOne(1);
        if (!$model) $model = new Km();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', "Сохранено"));
            return $this->redirect(['settings']);
        } else {

            $_routes = [];

            /** @var \app\modules\admin\models\Route $route */
            $routes = Route::find()->where(['status' => Route::STATUS_ACTIVE])->all();
            if ($routes && count($routes) > 0) foreach ($routes as $route) {
                $_routes[$route->id] = $route->title . " ({$route->base_tariff})";
            }

            $days = Yii::$app->params['schedules'];

            return $this->render('settings', [
                'model' => $model,
                'routes' => $_routes,
                'days' => $days
            ]);
        }
    }
}
