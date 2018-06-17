<?php
///**
// * Created by PhpStorm.
// * User: Graf
// * Date: 11.04.2017
// * Time: 14:13
// */
//
//namespace app\components\widgets;
//
//
//use yii\helpers\Html;
//use yii\helpers\Json;
//use yii\web\JsExpression;
//use yii\web\View;
//use zxbodya\yii2\elfinder\ElFinderAsset;
//
//class ElFinderInput extends \zxbodya\yii2\elfinder\ElFinderInput
//{
//    public $removeBtn = false;
//
//    public function init()
//    {
//        parent::init();
//    }
//
//    public function run()
//    {
//        if (!isset($this->options['id'])) {
//            $this->options['id'] = $this->getId();
//        }
//
//        $contoptions = $this->options;
//        $contoptions['id'] = $this->options['id'] . 'container';
//        echo Html::beginTag('div', $contoptions);
//        $inputOptions = array('id' => $this->options['id'], 'class' => 'form-control', 'readonly' => 'readonly');
//        if ($this->hasModel()) {
//            echo Html::activeTextInput($this->model, $this->attribute, $inputOptions);
//        } else {
//            echo Html::textInput($this->name, $this->value, $inputOptions);
//        }
//
//
//        echo Html::beginTag('div', ['style'=>'margin-top: 10px;']);
//        echo Html::button('Select', array('id' => $this->options['id'] . 'browse', 'class' => 'btn ink-reaction btn-info'));
//        if($this->removeBtn){
//            echo Html::button('Delete', array('id' => $this->options['id'] . 'delBtn', 'class' => 'btn ink-reaction btn-danger', 'style' => 'margin-left: 10px;'));
//        }
//        echo Html::endTag('div');
//
//        echo Html::endTag('div');
//
//        $settings = array_merge(
//            array(
//                'places' => "",
//                'rememberLastDir' => false,
//            ),
//            $this->settings
//        );
//
//        $settings['dialog'] = array(
//            'zIndex' => 400001,
//            'width' => 900,
//            'modal' => true,
//            'title' => "File manager",
//            'lang' => 'en'
//        );
//        $settings['editorCallback'] = new JsExpression('function(url) {$(\'#\'+aFieldId).attr(\'value\',url);}');
//        $settings['closeOnEditorCallback'] = true;
//        $connectorUrl = Json::encode($this->settings['url']);
//        $settings = Json::encode($settings);
//        $script = <<<EOD
//        window.elfinderBrowse = function(field_id, connector) {
//            var aFieldId = field_id, aWin = this;
//            if($("#elFinderBrowser").length == 0) {
//                $("body").append($("<div/>").attr("id", "elFinderBrowser"));
//                var settings = $settings;
//                settings["url"] = connector;
//                $("#elFinderBrowser").elfinder(settings);
//            }
//            else {
//                $("#elFinderBrowser").elfinder("open", connector);
//            }
//        };
//        window.delBtn = function(input) {
//            //$(input).val('')
//            $(input).attr('value','')
//        };
//EOD;
//
//        $view = $this->getView();
//        ElFinderAsset::register($view);
//        $view->registerJs($script, View::POS_READY, $key = 'ServerFileInput#global');
//
//        $js = //'$("#'.$id.'").focus(function(){window.elfinderBrowse("'.$name.'")});'.
//            '$("#' . $this->options['id'] . 'browse").click(function(){window.elfinderBrowse("' . $this->options['id'] . '", ' . $connectorUrl . ')});'.
//            '$("#' . $this->options['id'] . 'delBtn").click(function(){window.delBtn("#' . $this->options['id'] . '")});';
//
//
//        $view->registerJs($js);
//    }
//}