<?php
use yii\widgets\Breadcrumbs;
use yii\grid\GridView;

$this->title = Yii::$app->mv->gt("Рабочий стол",[],0);
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/adminlte/plugins/morris/morris.min.js', ['depends' => [\app\modules\admin\assets\JQueryAsset::className()]]);
$this->registerJsFile('@web/adminlte/dist/js/pages/dashboard.js', ['depends' => [\app\modules\admin\assets\JQueryAsset::className()]]);

$statuses = Yii::$app->params['statuses'];
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
</div>