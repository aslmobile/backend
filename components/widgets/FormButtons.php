<?php
/**
 * Created by PhpStorm.
 * User: demian
 * Date: 26.04.18
 * Time: 12:35
 */

namespace app\components\widgets;

use yii\bootstrap\Widget;

class FormButtons extends Widget {

    public $model;
    public $topButtons = false;

    public function run()
    {
        if ($this->topButtons)
            return $this->render('top_buttons', ['model' => $this->model]);

        $backUrl = preg_match('/\/index/', \Yii::$app->controller->route) == 1
            ? \Yii::$app->urlManager->createUrl('/admin')
            : \Yii::$app->urlManager->createUrl([\Yii::$app->controller->id . '/index']);
        return $this->render('buttons', ['model' => $this->model, 'backUrl' => $backUrl]);
    }

}