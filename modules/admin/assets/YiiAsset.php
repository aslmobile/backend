<?php
/**
 * Created by PhpStorm.
 * User: demian
 * Date: 27.04.18
 * Time: 11:23
 */

namespace app\modules\admin\assets;


class YiiAsset extends \yii\web\YiiAsset {

    public $depends = [
        'app\modules\admin\assets\JQueryAsset'
    ];
}