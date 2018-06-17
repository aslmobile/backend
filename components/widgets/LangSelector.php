<?php

namespace app\components\widgets;
use app\modules\admin\models\Lang;

class LangSelector extends \yii\bootstrap\Widget
{
    public function init(){}

    public function run() {
        return $this->render('langSelector', [
            'current' => Lang::getCurrent(),
            'langs' => Lang::find()->where('id != :current_id', [':current_id' => Lang::getCurrent()->id])->all(),
        ]);
    }
}