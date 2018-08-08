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
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="box">
                    <div class="box-body text-uppercase"><strong><?= Yii::t('app' , "Автомобили"); ?></strong></div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header"><?= \app\models\Vehicles::find()->count(); ?></h5>
                                    <span class="description-text"><?= Yii::t('app' , "Зарегистрировано"); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header"><?= \app\models\Vehicles::find()->where(['status' => \app\models\Vehicles::STATUS_WAITING])->count(); ?></h5>
                                    <span class="description-text"><?= Yii::t('app' , "Ждут одобрения"); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 class="description-header"><?= \app\models\Vehicles::find()->where(['status' => \app\models\Vehicles::STATUS_APPROVED])->count(); ?></h5>
                                    <span class="description-text"><?= Yii::t('app' , "Одобрено"); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12"></div>
            <div class="col-md-4 col-sm-6 col-xs-12"></div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <ul class="products-list product-list-in-box">
                            <?php $vehicles = \app\modules\admin\models\Vehicles::find()->orderBy(['created_at' => SORT_DESC])->limit(10)->all(); ?>
                            <?php if ($vehicles && count($vehicles) > 0) foreach ($vehicles as $vehicle) : ?>
                            <?php /** @var \app\models\Vehicles $vehicle */ ?>
                                <li class="item">
                                    <div class="product-img">
                                        <?php if (!empty ($vehicle->model->image)) : ?>
                                        <img src="<?= $vehicle->model->image; ?>" style="height: auto;" />
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-info">
                                        <?php switch ($vehicle->status)
                                        {
                                            case $vehicle::STATUS_WAITING: $vsc = 'orange'; break;
                                            case $vehicle::STATUS_APPROVED: $vsc = 'green-active'; break;
                                            case $vehicle::STATUS_ADDED: $vsc = 'aqua-active'; break;

                                            default: $vsc = 'aqua';
                                        }
                                        ?>
                                        <span class="badge bg-<?= $vsc; ?> pull-right"><?= $vehicle->statusList[$vehicle->status]; ?></span>
                                        <a href="<?= \yii\helpers\Url::toRoute('/admin/vehicles/update/' . $vehicle->id); ?>" class="product-title">
                                            <i class="fa fa-pencil"></i> &nbsp; <span class="badge bg-aqua-active" style="border-radius: 0;"><?= $vehicle->type->title; ?></span>
                                        </a>
                                        <span class="product-description">
                                            <span class="badge no-border" style="border-radius: 0;"><?= $vehicle->license_plate; ?></span>
                                            <span class="badge bg-aqua" style="border-radius: 0;"><?= $vehicle->vehicleName; ?></span>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center bg-gray-light">
                        <a href="<?= \yii\helpers\Url::toRoute('/admin/vehicles/index/'); ?>" class="uppercase"><?= Yii::t('app', "Список всех автомобилей"); ?></a>
                    </div>
                    <!-- /.box-footer -->
                </div>
            </div>
        </div>
    </div>
</div>