<?php

namespace app\modules\main\controllers;

use app\components\Controller;
use app\modules\main\models\Dynamic;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    public $layout = '@app/views/layouts/main';

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'timezone' => [
                'class' => 'yii2mod\timezone\TimezoneAction',
            ],
        ];
    }

    public function actionIndex($page = false)
    {
        if (!$page) {
            if (Yii::$app->user->isGuest) return $this->redirect(Url::toRoute('/user/default/login/'));
            return $this->redirect(Url::toRoute('/admin/'));
        }

        $this->layout = '@app/views/layouts/empty_page';

        $model = Dynamic::find()->multilingual()->where(['url' => $page])->one();

        if ($model === null) throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не найдена.'));

        return $this->renderPage($model);
    }

    private function renderPage($page)
    {
        return $this->render('page', ['model' => $page]);
    }
}
