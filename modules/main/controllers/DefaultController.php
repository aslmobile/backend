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
        if (Yii::$app->user->isGuest || !Yii::$app->user->can('admin')) return $this->goBack();
        $f_name = Yii::$app->request->get('model');
        $module = Yii::$app->request->get('module');
        if (!$module) $module = 'admin';
        $r_dir = $_SERVER['DOCUMENT_ROOT'] . '/modules/' . $module . '/models/';
        $m_dir = $_SERVER['DOCUMENT_ROOT'] . '/models/';
        if ($f_name) $f_name = ucfirst($f_name);

        if ($f_name && file_exists($m_dir . $f_name . '.php') && !file_exists($r_dir . $f_name . '.php')) {
            $new_file = <<<HTML
<?php

namespace app\modules\\$module\models;


class $f_name extends \app\models\\$f_name
{

}
HTML;
            file_put_contents($r_dir . $f_name . '.php', $new_file);
        } else die('uber fail');

    }

}
