<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Mailtpl */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Панель управления',[],false), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Шаблоны писем'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>&nbsp;</h1>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <div class="row">

            <div class="col-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua-active"><?= $model->title; ?></div>
                    <div class="box-body"><?= $model->descr; ?></div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-12 text-center">
                                <?= Html::a('Редактировать', [
                                    'update',
                                    'id' => $model->id
                                ], ['class' => 'btn btn-primary']) ?>
                                <?= Html::a('Удалить', [
                                    'delete',
                                    'id' => $model->id
                                ], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Вы действительно хотите удалить пользователя?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
