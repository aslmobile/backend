# V-jet engine 

## Модули сборки 1.120
### admin - `/modules/admin/`

* Управление пользователями
* Управление ролями пользователей
* Управление языками сайта
* Управление динамическими переводами  gt
* Управление статическими переводами i18n
* Управление динамическими страницами
* Управление иерархическим меню v.2.0
* Контент блоки (заголовок, изображение, описание)
* Meta tags everywhere
* Страны
* Города
* Регионы
* Управление глобальными настройками системы
* Генерация полей для моделей
* Файловый менеджер

### main - `/modules/main/`
* Главная страница сайта
* Обработчик ошибок

### user - `/modules/user/`
							
* Авторизация пользователей
* Регистрация пользователей
* Выход из системы
* Восстановление пароля

### api - `/modules/api/`

* RESTful api

### Расширения и компоненты сборки 1.120

* MemCache
* imageCache на любые разрешения `via composer`
* elFinder 1.2 - файловый менеджер `via composer`
* tinymce 4.1.5 - визуальный редактор `via composer`
* MultilingualBehavior - перевод динамического контента из базы данных
* Alert - вывод flash сообщений любого типа
* Slug - Yii2 slug behavior (транслитерация заголовка)

# Файлы локальных настроек

## Базовые настройки `/config/common-local.php`

```
<?php

require ('database.php');

$config = [
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'serializer' => 'SuperClosure\Serializer',
        ],
		'db' => $db,
        'mailer' => [
            'useFileTransport' => false,
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'app\modules\gii\Module';

    $config['modules']['gii'] = [
        'class' => 'app\modules\gii\Module',
        'allowedIPs' => ['*'],
        'generators' => [
            'crud' => [
                'class' => 'modules\gii\generators\crud\Generator',
                'templates' => [
                    'myCrud' => '@app/myTemplates/crud/default',
                ]
            ]
        ]
    ];
}

return $config;
```

## Базовые настройки `/config/console-local.php`

```
<?php
require ('database.php');

return [
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'serializer' => 'SuperClosure\Serializer',
        ],
        'db' => $db,
        'mailer' => [
            'useFileTransport' => false,
        ]
    ]
];
```

## Базовые настройки `/config/params-local.php`

```
<?php // Extended params.php settings for local server
return [];
```

## Базовые настройки `/config/database.php`

```
<?php
$db = [
    'dsn' => 'mysql:host=127.0.0.1:3306;dbname=DATABASE',
    'username' => 'DATABASE_USER',
    'password' => 'DATABASE_PASSWORD',
    'emulatePrepare' => false,
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
```

## Composer initial

~~~~
 composer update fxp/composer-asset-plugin --no-plugins 
 composer global require "fxp/composer-asset-plugin"
 
 composer install
~~~~