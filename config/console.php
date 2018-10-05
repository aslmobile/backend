<?php

use yii\helpers\ArrayHelper;

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
Yii::setAlias('@webroot', dirname(__DIR__) . '/web');

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'basic-console',
    'name' => 'ASL',
    'language' => 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        //'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'mv' => [
            'class' => 'app\components\Mv',
        ],
        'push' => [
            'class' => 'app\components\Push',
            'options' => [
                'returnInvalidTokens' => true //default false
            ],
//            'apnsConfig' => [
//                'environment' => '.sandbox',
//                'pem' => dirname(__DIR__).'/components/certs/DevPush.pem',
//                'passphrase' => 'lyres374;fascia', //optional
//            ],
            'fcmConfig' => [
                1 => ['apiAccessKey' => 'AIzaSyCzwjm0emzf4aVUdjPTAfn0fhk79cdJ3Jc'],
                2 => ['apiAccessKey' => 'AIzaSyDjpbHCJFPPWOE8hx2PHmufI5tVS-XuGPE']
            ],
//            'gcmConfig' => [
//                'apiAccessKey' => 'AIzaSyCUfMeKft1Vw1YGxGYw7AMNEMBxw082Eno'
//            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
            'enableSession' => false,
        ],
        'imageCache' => [
            'class' => 'corpsepk\yii2imagecache\ImageCache',
            'cachePath' => '@app/web/files/cache',
            'cacheUrl' => '/files/cache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceMessageTable'=>'{{%source_message}}',
                    'messageTable'=>'{{%message}}',
                    'sourceLanguage' => 'ru-RU',
                    'enableCaching' => true,
                    'cachingDuration' => 3600
                ],
            ]
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:Y-m-d',
            'datetimeFormat' => 'php:Y-m-d H:i:s',
            'timeFormat' => 'php:H:i:s',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
