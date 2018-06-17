<?php
use app\models\Lang;

$l = Lang::getCurrent();
$lcur = $l->name;
$isfront = ((Yii::$app->request->url == '/') || (Yii::$app->request->url == '/' . Yii::$app->controller->lang)) ? 1 : 0;
$menu_internal_prefix = ($l->default) ? '/' : "/" . $l->url . "/";
?>