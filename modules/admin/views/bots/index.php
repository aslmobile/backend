<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */

$this->title = Yii::$app->mv->gt('Боты', [], 0);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h2><?= Html::encode($this->title) ?></h2>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua">
                        <h3 class="box-title"><?= Yii::t('app', "Бот водителя"); ?></h3>
                    </div>
                    <div class="box-footer text-center bg-gray-light">
                        <?= Html::a(Yii::t('app', "Перейти к настройкам"), \yii\helpers\Url::toRoute(['/admin/bots/driver']), []); ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua">
                        <h3 class="box-title"><?= Yii::t('app', "Бот пассажира"); ?></h3>
                    </div>
                    <div class="box-footer text-center bg-gray-light">
                        <?= Html::a(Yii::t('app', "Перейти к настройкам"), \yii\helpers\Url::toRoute(['/admin/bots/passenger']), []); ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua">
                        <h3 class="box-title"><?= Yii::t('app', "Транзакции"); ?></h3>
                    </div>
                    <div class="box-footer text-center bg-gray-light">
                        <?= Html::a(Yii::t('app', "Перейти к настройкам"), \yii\helpers\Url::toRoute(['/admin/bots/transactions']), []); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
