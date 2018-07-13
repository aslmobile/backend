<?php
$params = [
    'adminEmail' => 'liervitarr@gmail.com',
	'defTitle' => 'ASL',
	'defDescription' => 'ASL',
    'supportEmail' => 'support@aslmobile.net',
	'sourceLanguage' => 'ru',
	'user.passwordResetTokenExpire' => 3600,
	'use_russian_titles' => 0,
	'limit_items' => 12,
    'siteUrl' => 'https://aslmobile.net',
	'allowedIps' => [
        '127.0.0.1', '217.24.160.240', '217.24.160.195'
    ],
    'db_content_type' => [
        0 => 'image_count',
        1 => 'video_count',
    ],
    'image_extensions' => 'bmp,gif,jpeg,jpg,jpe,jp2,png',
    'video_extensions' => 'avi,mpeg,mp4,mkv,flv',
    'api_salt' => "e54d713d1a6afca4305874c9a7bea030d5feeab3b61369bb7c940e6cfb7aa14e",
    'content_status' => [
        0 => Yii::t('app', 'Включено'),
        1 => Yii::t('app', 'Отключено'),
    ],
    'content_status_video' => [
        0 => Yii::t('app', 'Включено'),
        1 => Yii::t('app', 'Отключено'),
    ],
    'status' => [
        0 => Yii::t('app', "Активно"),
        1 => Yii::t('app', "Неактивно"),
    ],
    'statuses' => [
        0 => Yii::t('app',"Pending approval"),
        1 => Yii::t('app',"Approved"),
        9 => Yii::t('app',"Blocked"),
    ],
    'feedback' => [
        0 => Yii::t('app',"Created"),
        1 => Yii::t('app',"Process"),
        2 => Yii::t('app',"Processed"),
    ],
    'user_type' => [
        0 => Yii::t('app',"User"),
        1 => Yii::t('app',"Admin"),
        2 => Yii::t('app',"Manager"),
        3 => Yii::t('app',"Driver"),
        4 => Yii::t('app',"Passenger"),
    ],
    'block_duration' => [
        0 => Yii::t('app','1 week'),
        1 => Yii::t('app','2 weeks'),
        2 => Yii::t('app','1 months'),
        10 => Yii::t('app','Forever'),
    ],
    'block_reasons' => [
        0 => Yii::t('app','Offensive content'),
        1 => Yii::t('app','Spam'),
    ],
    'gender' => [
        0 => Yii::t('app',"male"),
        1 => Yii::t('app',"female"),
        2 => Yii::t('app',"other"),
    ],
    'auth2arr' => [
        Yii::t('app',"Off"),
        Yii::t('app',"SMS"),
        Yii::t('app',"Google"),
        Yii::t('app',"Email"),
    ],
    'yesno' => [
        Yii::t('app',"No"),
        Yii::t('app',"Yes")
    ],
    'requireEmailValidation' => true,
    'weekdays' => [
        Yii::t('app',"Sunday"),
        Yii::t('app',"Monday"),
        Yii::t('app',"Tuesday"),
        Yii::t('app',"Wednesday"),
        Yii::t('app',"Thursday"),
        Yii::t('app',"Friday"),
        Yii::t('app',"Saturday"),
    ],
    'months' => [
        Yii::t('app',"January"),
        Yii::t('app',"February"),
        Yii::t('app',"March"),
        Yii::t('app',"April"),
        Yii::t('app',"May"),
        Yii::t('app',"June"),
        Yii::t('app',"July"),
        Yii::t('app',"August"),
        Yii::t('app',"September"),
        Yii::t('app',"October"),
        Yii::t('app',"November"),
        Yii::t('app',"December"),
    ],
    'months_video' => [
        Yii::t('app',"Января"),
        Yii::t('app',"Февраля"),
        Yii::t('app',"Марта"),
        Yii::t('app',"Апреля"),
        Yii::t('app',"Мая"),
        Yii::t('app',"Июня"),
        Yii::t('app',"Июля"),
        Yii::t('app',"Авгуса"),
        Yii::t('app',"Сентября"),
        Yii::t('app',"Октября"),
        Yii::t('app',"Ноября"),
        Yii::t('app',"Декабря"),
    ],
    'months_en' => [
        Yii::t('app',"January"),
        Yii::t('app',"February"),
        Yii::t('app',"March"),
        Yii::t('app',"April"),
        Yii::t('app',"May"),
        Yii::t('app',"June"),
        Yii::t('app',"July"),
        Yii::t('app',"August"),
        Yii::t('app',"September"),
        Yii::t('app',"October"),
        Yii::t('app',"November"),
        Yii::t('app',"December"),
    ],
    'cancel-trip-reasons' => [
        1 => Yii::t('app',"Причина 1"),
        2 => Yii::t('app',"Причина 2"),
        3 => Yii::t('app',"Причина 3"),
        4 => Yii::t('app',"Причина 4"),
        5 => Yii::t('app',"Причина 5")
    ],
    'cancel-passenger-reasons' => [
        1 => Yii::t('app',"Причина 1"),
        2 => Yii::t('app',"Причина 2"),
        3 => Yii::t('app',"Причина 3"),
        4 => Yii::t('app',"Причина 4"),
        5 => Yii::t('app',"Причина 5")
    ]
];
return $params;
