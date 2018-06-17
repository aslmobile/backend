<?php

namespace app\modules\main\controllers;

use app\components\Controller;
use app\models\Category;
use app\models\Downloads;
use app\models\Feedback;
use app\models\Products;
use app\modules\main\models\Dynamic;
use Yii;
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
        if (!$page) return $this->render('index');

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

    public function actionProducts($category_id)
    {
        $products = Products::find()
            ->where(Products::tableName() .'.status = '. Products::STATUS_ACTIVE)
            ->andWhere(Products::tableName() .'.category_id = '. $category_id)
            /*->orderBy('created_at DESC, sort ASC')*/->multilingual()
            ->orderBy([
                'created_at' => SORT_ASC,
                'sort' => SORT_ASC
            ])
            ->all();
        $category = Category::find()->where(['id' => $category_id])->one();
        return $this->render('products', ['products' => $products, 'category' => $category]);
    }

    public function actionDownloads() {
        $downloads = Downloads::find()
            ->where(Downloads::tableName() .'.status = '. Downloads::STATUS_ACTIVE)
            ->with('category')
            /*->orderBy('created_at DESC, sort ASC')*/->multilingual()
            ->orderBy([
                'category_id' => SORT_ASC,
                'sort' => SORT_ASC
            ])
            ->all();

        return $this->render('downloads', ['downloads' => $downloads]);
    }

    public function actionFeedback()
    {
        $model = new Feedback();
        if (Yii::$app->request->isAjax) {
            $model->name = Yii::$app->request->post('name');
            $model->email = Yii::$app->request->post('email');
            $model->message = Yii::$app->request->post('message');
            if ($model->save()) {
                return true;
            }
        }

        return $this->render('page', compact('form_model'));
    }

    public function actionContact()
    {
        return $this->render('contact');
    }
}
