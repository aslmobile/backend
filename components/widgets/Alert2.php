<?php

namespace app\components\widgets;

use Yii;

class Alert2 extends \yii\bootstrap\Widget
{
    public $alertTypes = [
        'error'   => 'Ошибка',
        'danger'  => 'Внимание',
        'success' => 'Операция выполнена успешно',
        'info'    => 'Информация',
        'warning' => 'Предупреждение',
    ];

    public $closeButton = [];
    public function init()
    {
        parent::init();
        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes();
        $appendCss = isset($this->options['class']) ? ' ' . $this->options['class'] : '';
        foreach ($flashes as $type => $data) {
			$title = (isset($this->alertTypes[$type]))?$this->alertTypes[$type]:$this->alertTypes['success'];
			echo '<script>showNotify("'.$title.'", "'.$data.'");</script>';
        }
    }
}