<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Tune */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tunes', 'url' => ['index']];
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
            <div class="col-sm-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3></h3>
                        <div class="box-tools pull-right">
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
                                    'confirm' => 'Вы действительно хотите удалить?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <?= DetailView::widget([
                                    'model' => $model,
                                    'attributes' => [
                                        'id',
                                        'type',
                                        'val:html',
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
