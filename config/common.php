<?php

use yii\helpers\ArrayHelper;

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$config = [
    'id' => 'v-jet',
    'name' => 'ASL',
    'defaultRoute' => 'main/default/index',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
        'main' => [
            'class' => 'app\modules\main\Module',
        ],
        'gii' => [
            'class' => 'app\modules\gii\Module',
        ],
    ],
    'components' => [
        'mv' => [
            'class' => 'app\components\Mv',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'basePath' => '@webroot',
                    'baseUrl' => '@web',
                    'js' => [
                        'adminlte/plugins/jQuery/jquery-2.2.3.min.js',
                    ]
                ],
                'yii\jui\JuiAsset' => [
                    'sourcePath' => null,
                    'basePath' => '@webroot',
                    'baseUrl' => '@web',
                    'css' => [

                    ],
                    'js' => [
                        'adminlte/plugins/jQueryUI/jquery-ui.min.js',
                    ]
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => 'wkxTVJpPd324422zA_BWWUp15TYnIp_c3',
            'class' => 'app\components\LangRequest',
            'baseUrl' => '',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceMessageTable' => '{{%source_message}}',
                    'messageTable' => '{{%message}}',
                    'sourceLanguage' => 'en-En',
                    'enableCaching' => true,
                    'cachingDuration' => 3600
                ],
            ]
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/default/login'],
        ],
        'imageCache' => [
            'class' => 'corpsepk\yii2imagecache\ImageCache',
            'cachePath' => '@app/web/files/cache',
            'cacheUrl' => '/files/cache',
        ],
        'urlManager' => require(__DIR__ . '/url-manager-config.php'),
        'errorHandler' => [
            'errorAction' => 'main/default/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:d.m.Y H:i:s',
            'timeFormat' => 'php:H:i:s',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
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

return $config;
