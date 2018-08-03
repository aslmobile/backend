<?php
/**
 * Created by PhpStorm.
 * User: Graf
 * Date: 21.08.2017
 * Time: 16:10
 */

$sp = '<_s:[\/]?>';

return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'class' => 'app\components\LangUrlManager',
    'rules' => [
        'admin' => 'admin/default/index',

        '<page:[\w\-]+>' => 'main/default/index',

        ///DEFAULT
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' . $sp => '<_m>/<_c>/<_a>',

        '<_m:[\w\-]+>/<_c:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>/<parms:[\w\-]+>' . $sp => '<_m>/<_c>/<_a>',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>' . $sp => '<_m>/<_c>/<_a>',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<id:\d+>' . $sp => '<_m>/<_c>/view',
        '<_m:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>/<parms:[\w\-]+>' . $sp => '<_m>/default/<_a>',
        '<_m:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>' . $sp => '<_m>/default/<_a>',
        '<_m:[\w\-]+>/<id:\d+>' . $sp => '<_m>/default/view',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>' . $sp => '<_m>/<_c>/<_a>',
        '<_m:[\w\-]+>/<_c:[\w\-]+>' . $sp => '<_m>/<_c>/index',

        //DEFAULT
        '<_m:(admin)>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/<_a>',
        '<_m:(admin)>/<_c:[\w\-]+>/<_a:[\w\-]+>' => '<_m>/<_c>/<_a>',
        '<_m:(admin)>/<_c:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/view',
        '<_m:(admin)>' => '<_m>/default/index',
        '<_m:(admin)>/<_c:[\w\-]+>' => '<_m>/<_c>/index',
    ],
];
