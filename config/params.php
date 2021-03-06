<?php
$params = [
    'adminEmail' => 'liervitarr@gmail.com',
    'defTitle' => 'ASL',
    'defDescription' => 'ASL',
    'supportEmail' => 'admin@aslmobile.net',
    'sourceLanguage' => 'ru',
    'user.passwordResetTokenExpire' => 3600,
    'use_russian_titles' => 0,
    'limit_items' => 12,
    'siteUrl' => 'https://aslmobile.net',
    'allowedIps' => [
        '127.0.0.1',

        // V-JET GROUP
        '217.24.160.240', '217.24.160.195', '217.24.161.64',

        // Alexandr Tsymbal
        '93.75.225.50', '93.75.237.182', '141.170.240.176', '93.75.236.177', '77.120.40.138',

        // Kostya Batrak
        '46.211.6.142', '46.119.87.237', '91.202.132.127', ' 94.179.232.195',

        // Andrey Tregubenko
        '134.249.138.232'
    ],
    'db_content_type' => [
        0 => 'image_count',
        1 => 'video_count',
    ],
    'main_vehicle_yes_no' => [
        0 => Yii::t('app', "Вторичная машина"),
        1 => Yii::t('app', "Основная машина")
    ],
    'image_extensions' => 'bmp,gif,jpeg,jpg,jpe,jp2,png',
    'video_extensions' => 'avi,mpeg,mp4,mkv,flv',
    'api_salt' => "e54d713d1a6afca4305874c9a7bea030d5feeab3b61369bb7c940e6cfb7aa14e",
    'qr_api_url' => "https://chart.googleapis.com/chart?cht=qr&chs=500x500&choe=UTF-8&chl={data}",
    'yes_no' => [
        1 => Yii::t('app', "Да"),
        0 => Yii::t('app', "Нет")
    ],
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
        0 => Yii::t('app', "Ждет одобрения"),
        1 => Yii::t('app', "Одобрен"),
        2 => Yii::t('app', "Регистрация")
    ],
    'feedback' => [
        0 => Yii::t('app', "Created"),
        1 => Yii::t('app', "Process"),
        2 => Yii::t('app', "Processed"),
    ],
    'user_type' => [
        0 => Yii::t('app', "Пользователь"),
        1 => Yii::t('app', "Администратор"),
        2 => Yii::t('app', "Диспетчер"),
        3 => Yii::t('app', "Водитель"),
        4 => Yii::t('app', "Пассажир"),
    ],
    'block_duration' => [
        0 => Yii::t('app', '1 week'),
        1 => Yii::t('app', '2 weeks'),
        2 => Yii::t('app', '1 months'),
        10 => Yii::t('app', 'Forever'),
    ],
    'block_reasons' => [
        0 => Yii::t('app', 'Offensive content'),
        1 => Yii::t('app', 'Spam'),
    ],
    'gender' => [
        0 => Yii::t('app', "Мужской"),
        1 => Yii::t('app', "Женский"),
        2 => Yii::t('app', "Не указан"),
    ],
    'auth2arr' => [
        Yii::t('app', "Off"),
        Yii::t('app', "SMS"),
        Yii::t('app', "Google"),
        Yii::t('app', "Email"),
    ],
    'yesno' => [
        Yii::t('app', "No"),
        Yii::t('app', "Yes")
    ],
    'requireEmailValidation' => true,
    'weekdays' => [
        1 => Yii::t('app', "Monday"),
        2 => Yii::t('app', "Tuesday"),
        3 => Yii::t('app', "Wednesday"),
        4 => Yii::t('app', "Thursday"),
        5 => Yii::t('app', "Friday"),
        6 => Yii::t('app', "Saturday"),
        7 => Yii::t('app', "Sunday"),
    ],
    'months' => [
        Yii::t('app', "January"),
        Yii::t('app', "February"),
        Yii::t('app', "March"),
        Yii::t('app', "April"),
        Yii::t('app', "May"),
        Yii::t('app', "June"),
        Yii::t('app', "July"),
        Yii::t('app', "August"),
        Yii::t('app', "September"),
        Yii::t('app', "October"),
        Yii::t('app', "November"),
        Yii::t('app', "December"),
    ],
    'months_video' => [
        Yii::t('app', "Января"),
        Yii::t('app', "Февраля"),
        Yii::t('app', "Марта"),
        Yii::t('app', "Апреля"),
        Yii::t('app', "Мая"),
        Yii::t('app', "Июня"),
        Yii::t('app', "Июля"),
        Yii::t('app', "Авгуса"),
        Yii::t('app', "Сентября"),
        Yii::t('app', "Октября"),
        Yii::t('app', "Ноября"),
        Yii::t('app', "Декабря"),
    ],
    'months_en' => [
        Yii::t('app', "January"),
        Yii::t('app', "February"),
        Yii::t('app', "March"),
        Yii::t('app', "April"),
        Yii::t('app', "May"),
        Yii::t('app', "June"),
        Yii::t('app', "July"),
        Yii::t('app', "August"),
        Yii::t('app', "September"),
        Yii::t('app', "October"),
        Yii::t('app', "November"),
        Yii::t('app', "December"),
    ],
    'cancel-trip-reasons' => [
        ['id' => 1, 'value' => Yii::t('app', "Причина 1")],
        ['id' => 2, 'value' => Yii::t('app', "Причина 2")],
        ['id' => 3, 'value' => Yii::t('app', "Причина 3")],
        ['id' => 4, 'value' => Yii::t('app', "Причина 4")],
        ['id' => 5, 'value' => Yii::t('app', "Причина 5")]
    ],
    'cancel-passenger-reasons' => [
        ['id' => 1, 'value' => Yii::t('app', "Причина 1")],
        ['id' => 2, 'value' => Yii::t('app', "Причина 2")],
        ['id' => 3, 'value' => Yii::t('app', "Причина 3")],
        ['id' => 4, 'value' => Yii::t('app', "Причина 4")],
        ['id' => 5, 'value' => Yii::t('app', "Причина 5")]
    ],
    'blacklist' => [
        'rating' => [
            'comment' => Yii::t('app', "Низкий уровень рейтинга"),
            'description' => Yii::t('app', "Заблокирован за низкий рейтинг"),
            'reason' => Yii::t('app', "Низкий уровень рейтинга"),
            'notification' => Yii::t('app', "Вы были заблокированы за низкий уровень рейтинга")
        ]
    ],
    'schedules' => [
        1 => Yii::t('app', "Понедельник"),
        2 => Yii::t('app', "Вторник"),
        3 => Yii::t('app', "Среда"),
        4 => Yii::t('app', "Четверг"),
        5 => Yii::t('app', "Пятница"),
        6 => Yii::t('app', "Суббота"),
        7 => Yii::t('app', "Воскресенье"),
    ],
    'distance' => 217
];
return $params;
