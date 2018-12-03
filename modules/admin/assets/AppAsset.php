<?php

namespace app\modules\admin\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot/modules/admin/assets';
    public $baseUrl = '@web/adminlte';
    public $css = [
        "dist/css/AdminLTE.min.css",
        "dist/css/skins/_all-skins.min.css",
        "plugins/jQueryUI/jquery-ui.min.css",
        "plugins/iCheck/flat/blue.css",
        "plugins/iCheck/all.css",
        "plugins/morris/morris.css",
        "plugins/jvectormap/jquery-jvectormap-1.2.2.css",
        "plugins/datepicker/datepicker3.css",
        "plugins/daterangepicker/daterangepicker.css",
        "plugins/x-editable/bootstrap3-editable/css/bootstrap-editable.css",
        "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css",
        '/admin_assets/css/all.css'
    ];
    public $js = [
        "https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js",
        "plugins/iCheck/icheck.min.js",
        "plugins/sparkline/jquery.sparkline.min.js",
        "plugins/jvectormap/jquery-jvectormap-1.2.2.min.js",
        "plugins/jvectormap/jquery-jvectormap-world-mill-en.js",
        "plugins/knob/jquery.knob.js",
        "https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js",
        "plugins/daterangepicker/daterangepicker.js",
        "plugins/datepicker/bootstrap-datepicker.js",
        "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js",
        "plugins/slimScroll/jquery.slimscroll.min.js",
        "plugins/fastclick/fastclick.js",
        //"plugins/x-editable/bootstrap3-editable/js/bootstrap-editable.min.js",
        "plugins/jquery.tmpl.min.js",
        "plugins/select2/select2.full.js",
        "/admin_assets/js/jquery.blockUI.js",
        "dist/js/users.js",
        "plugins/jscolor-2.0.4/jscolor.min.js",
        "dist/js/save-sidebar-collapse.js",
        "dist/js/app.min.js",
        "dist/js/all.js",
        "/admin_assets/js/all.js",
        '/admin_assets/js/checkpoint.js',
        '/admin_assets/js/reconnecting-websocket.min.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];

    public function init() {
        parent::init();

        \Yii::$app->assetManager->bundles['yii\\bootstrap\\BootstrapAsset'] = [
            'basePath' => '@webroot',
            'baseUrl' => '@web',
            'css' => [
                'adminlte/bootstrap/css/bootstrap.min.css',
            ],
        ];

        \Yii::$app->assetManager->bundles['yii\\bootstrap\\BootstrapPluginAsset'] = [
            'basePath' => '@webroot',
            'baseUrl' => '@web',
            'js' => [
                'adminlte/bootstrap/js/bootstrap.min.js',
            ]
        ];
    }
}
