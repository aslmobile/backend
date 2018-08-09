<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\Legal */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', "Пользовательское соглашение"), 'url' => ['index']];
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
            <div class="col-sm-12 col-md-10 col-md-offset-1">
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua"><?= $model->title; ?></div>
                    <div class="box-body"><?= nl2br($model->content); ?></div>
                    <div class="box-footer text-center">
                        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post'
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
