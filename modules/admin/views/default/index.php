<?php
use yii\widgets\Breadcrumbs;
use yii\grid\GridView;

$this->title = Yii::$app->mv->gt("Рабочий стол",[],0);
$this->params['breadcrumbs'][] = $this->title;
//
//$this->registerJsFile('@web/adminlte/plugins/morris/morris.min.js', ['depends' => ['yii\web\JqueryAsset']]);
//$this->registerJsFile('@web/adminlte/dist/js/pages/dashboard.js', ['depends' => ['yii\web\JqueryAsset']]);

$statuses = Yii::$app->params['statuses'];
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="container-fluid">
        <h2><?= Yii::t('app' , "Панель управления"); ?></h2>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-car"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', "Зарегистрировано автомобилей"); ?></span>
                        <span class="info-box-number"><?= \app\models\Vehicles::find()->count(); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-user-secret"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', "Зарегистрировано водителей"); ?></span>
                        <span class="info-box-number"><?= \app\models\User::find()->where(['type' => \app\modules\api\models\Users::TYPE_DRIVER])->count(); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', "Зарегистрировано пассажиров"); ?></span>
                        <span class="info-box-number"><?= \app\models\User::find()->where(['type' => \app\modules\api\models\Users::TYPE_PASSENGER])->count(); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-arrows-h"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', "Совершено поездок"); ?></span>
                        <span class="info-box-number"><?= \app\models\Trip::find()->count(); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>