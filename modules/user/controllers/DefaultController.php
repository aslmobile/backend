<?php

namespace app\modules\user\controllers;

use app\models\Comment;
use app\models\Countries;
use app\models\Feed;
use app\models\Hashtags;
use app\models\Menu;
use app\models\Notifies;
use app\models\Order;
use app\models\Organization;
use app\models\Prices;
use app\models\Relation;
use app\models\Schedule;
use app\models\Team;
use app\models\Wsroom;
use app\modules\admin\models\Platform;
use app\modules\user\models\ConfirmEmailForm;
use app\modules\user\models\LoginForm;
use app\modules\user\models\PasswordResetRequestForm;
use app\modules\user\models\ResetPasswordForm;
use app\modules\user\models\SignupForm;
use app\modules\user\models\UserSoc;
use app\modules\user\models\User;
use app\models\Event;
use app\models\Object;

use app\models\DirSport;
use app\models\DirPlatformType;
use app\models\DirPlatform;
use app\models\DataOptions;

use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use app\components\Controller;
use Yii;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'logout',
                ],
                'rules' => [				
                    [
                        'actions' => [
                            'login'
                        ],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                          'logout',
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            /*'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],*/
        ];
    }
 
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionLogin()
    {
        $this->layout = '@app/views/layouts/empty';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $pas = '123qwerty789';
        $hash = Yii::$app->getSecurity()->generatePasswordHash($pas);
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
                'hash'  => $hash,
            ]);
        }
    }
/*
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return json_encode(['popup' => Yii::$app->mv->gt('Login success'), 'redirect' => Url::toRoute('/'), 'redirectTO' => 1000]);
            } else {
                return json_encode($model->getErrors());
            }
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
 */
    public function actionLogout()
    {
        Yii::$app->user->logout();
 
        return $this->goHome();
    }



	public function loadModel($id){
	    if(!$id) $id = Yii::$app->user->id;

        if(!($model = User::findOne($id))){
            Yii::$app->controller->throw404();
        }
        return $model;
    }



}
