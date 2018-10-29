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
                1 => ['apiAccessKey' => 'AAAACWlobmk:APA91bEdbIyq3nutK_S7-KH1wjDizQKqGGsBeVu4l6YfN5zxp7ikCMIE5I6FOg5_hc_ccLYlR9_9ojuYYNXGRl13J1DIbizrS20REYOLPikdy289WzngQHr75kNkMRbeW86U3EPCbS3U'],
                2 => ['apiAccessKey' => 'AAAA45toJLA:APA91bHNZbgEI7rUFtLfXhmWhx2BB_vnpx5Y_XSCTutGR2Z6v42PXeUxsijPHxkzAHLUB9Uxp9V5culfr-yCby-7Oq_u484eN7zs3Gi8J3WT0VJJG-ZrUF_1Mq4NofWTLhGPEESgQl6p']
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
