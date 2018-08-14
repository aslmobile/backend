<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Line */

$this->title = Yii::t('app', "Поездка №{id}", ['id' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', "Поездки"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <div class="box box-widget">
                    <div class="box-header bg-aqua">
                        <h3 class="box-title"><?= $model->route->title; ?></h3>
                    </div>
                    <div class="box-header with-border bg-aqua-active">
                        <?= $model->startPoint->title; ?>
                    </div>
                    <div class="box-body no-padding">
                        <div class="row">
                            <div class="col-md-3">
                                <img class="img-responsive" style="max-height: 80px; position: absolute; right: -65px; top: 0; z-index: 101;" src="<?= $model->vehicle->model->image; ?>" alt="<?= $model->vehicle->model->title; ?>" />
                            </div>
                            <div class="col-md-9">
                                <div class="wrapper-body bg-gray-light" style="padding-top: 80px; background-color: #EAECEE;">
                                    <div class="row" style="padding: 15px;">
                                        <div class="col-md-6 text-uppercase"><span style="color: #9B9FA8;"><?= Yii::t('app', "Дата"); ?></span></div>
                                        <div class="col-md-6 text-right"><?= Yii::$app->formatter->asDate($model->starttime); ?></div>

                                        <div class="col-md-6 text-uppercase"><span style="color: #9B9FA8;"><?= Yii::t('app', "Время выезда"); ?></span></div>
                                        <div class="col-md-6 text-right"><?= Yii::$app->formatter->asTime($model->starttime); ?></div>

                                        <div class="col-md-6 text-uppercase"><span style="color: #9B9FA8;"><?= Yii::t('app', "Ожидание"); ?></span></div>
                                        <div class="col-md-6 text-right"><?= Yii::$app->formatter->asTime(($model->start_time - $model->created_at)); ?></div>

                                        <div class="col-md-6 text-uppercase"><span style="color: #9B9FA8;"><?= Yii::t('app', "В дороге"); ?></span></div>
                                        <div class="col-md-6 text-right"><?= Yii::$app->formatter->asTime(($model->end_time - $model->start_time)); ?></div>

                                        <div class="col-md-6 text-uppercase"><span style="color: #9B9FA8;"><?= Yii::t('app', "Тип машины"); ?></span></div>
                                        <div class="col-md-6 text-right"><?= $model->vehicle->type->title; ?></div>

                                        <div class="col-md-6 text-uppercase"><span style="color: #9B9FA8;"><?= Yii::t('app', "Пассажиров"); ?></span></div>
                                        <div class="col-md-6 text-right"><?= $model->passengers; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-8">
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua">
                        <h3 class="box-title"><?= Yii::t('app', "Пассажиры"); ?></h3>
                    </div>
                    <div class="box-body no-padding">
                        <ul class="users-list clearfix">
                        <?php foreach ($model->getPassengers(false) as $passenger) : ?>
                            <li>
                                <?= Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $passenger->user->getImageFile(), '128x128', ['class' => 'img-circle']) ?>
                                <a class="users-list-name" href="<?= \yii\helpers\Url::toRoute('/admin/user/view/' . $passenger->user->id); ?>"><?= $passenger->user->fullName; ?></a>
                                <span class="users-list-date"><?= Yii::$app->formatter->asDatetime($passenger->user->created_at, "php:d.m.Y  H:i:s"); ?></span>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
