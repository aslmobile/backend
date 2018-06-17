<?php

namespace app\components\widgets;

use Yii;
use yii\base\Widget;

class Metaform extends Widget
{

    public $lang = '';
    public $data_type = 0;

    public function init(){
    }

    public function run() {
            return $this->render('metaform', [
                'k' => $this->lang,
                'data_type' => $this->data_type,
            ]);
    }
}