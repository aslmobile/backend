<?php


namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/bootstrap.min.css',
        'css/main.min.css',
        //'css/select2.min.css',
        //'css/style.css',
        //'css/responsive.css',
        'css/site.css',
        'css/jquery-confirm.css',
    ];

    public $js = [
        'js/jquery-3.2.1.slim.min.js',
        'js/bootstrap.min.js',
        'js/scripts.min.js',
        'js/jquery-confirm.js',
//        'js/datatables.min.js',
        //'js/select2.min.js',
        //'js/owl.carousel.min.js',
        //'js/jquery.dotdotdot.js',
        //'js/my.js',
    ];

    public $depends = [
//        'yii\web\YiiAsset',
    ];
	
}