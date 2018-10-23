<?php

namespace app\modules\main\controllers;

use app\components\Controller;
use app\modules\main\models\Dynamic;
use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

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
        if (!$page)
        {
            if (Yii::$app->user->isGuest) return $this->redirect(Url::toRoute('/user/default/login/'));

            return $this->redirect(Url::toRoute('/admin/'));
        }

        $model = Dynamic::find()
            ->multilingual()
            ->where(['url' => $page])
            ->one();

        if ($model === null) throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не найдена.'));

        return $this->renderPage($model);
    }

    private function renderPage($page)
    {
        return $this->render('page', ['model' => $page]);
    }

    public function actionUbermodelextender()
    {
        $fname = Yii::$app->request->get('model');
        $module = Yii::$app->request->get('module');
        if (!$module) {
            $module = 'admin';
        }
        $rdir = $_SERVER['DOCUMENT_ROOT'] . '/modules/' . $module . '/models/';
        $mdir = $_SERVER['DOCUMENT_ROOT'] . '/models/';
        if ($fname) {
            $fname = ucfirst($fname);
        }
        if ($fname && file_exists($mdir . $fname . '.php') && !file_exists($rdir . $fname . '.php')) {
            $newfile = <<<HTML
<?php

namespace app\modules\\$module\models;


class $fname extends \app\models\\$fname
{

}
HTML;
            file_put_contents($rdir . $fname . '.php', $newfile);
        } else {
            die('uber nevernij fail');
        }
    }

}
