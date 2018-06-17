<?php

namespace app\modules\admin\controllers;

use alexantr\elfinder\ConnectorAction;
use alexantr\elfinder\InputFileAction;
use alexantr\elfinder\TinyMCEAction;
use app\components\Controller;
use Yii;

class ElFinderController extends Controller
{

    public function actions()
    {
        return [
            'connector' => [
                'class' => ConnectorAction::className(),
                'options' => [
                    'debug' => true,
                    'uploadTempPath' => Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . 'files/temp',
                    'roots' => [
                        [
                            'driver' => 'LocalFileSystem',
                            'path' => Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . 'files',
                            'URL' => Yii::getAlias('@web') . '/files/',
                            'mimeDetect' => 'internal',
                            'imgLib' => 'gd',
                            'accessControl' => function ($attr, $path) {
                                return (strpos(basename($path), '.') === 0) ?
                                    !($attr == 'read' || $attr == 'write') :
                                    null;
                            },
                        ],
                    ],
                ],
            ],
            'input' => [
                'class' => InputFileAction::className(),
                'connectorRoute' => 'connector',
            ],
            'tinymce' => [
                'class' => TinyMCEAction::className(),
                'connectorRoute' => 'connector',
            ],
        ];
    }
}
