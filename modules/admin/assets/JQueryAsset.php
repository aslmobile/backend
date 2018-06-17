<?php
/**
 * Created by PhpStorm.
 * User: demian
 * Date: 27.04.18
 * Time: 10:55
 */

namespace app\modules\admin\assets;


use yii\web\AssetBundle;

class JQueryAsset extends AssetBundle {

    public $sourcePath = null;
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'adminlte/plugins/jQuery/jquery-2.2.3.min.js',
        'adminlte/plugins/jQueryUI/jquery-ui.min.js'
    ];

}