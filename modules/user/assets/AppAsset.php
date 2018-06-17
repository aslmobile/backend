<?php

namespace app\modules\user\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot/modules/user/assets';
    public $baseUrl = '@web/adminlte';
    public $css = [
        'dist/css/AdminLTE.min.css',
        'plugins/iCheck/square/blue.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css',
        'dist/css/AdminLTE.min.css',
    ];
    public $js = [
        'plugins/iCheck/icheck.min.js',
    ];
	public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',	
    ];

}