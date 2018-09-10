<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\City */

$this->title = $model->title;
$this->params['breadcrumbs'][] = [
    'label' => 'Cities',
    'url' => ['index']
];
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
                            <?= \app\components\widgets\FormButtons::widget(['model' => $model, 'topButtons' => true]) ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <?= DetailView::widget([
                                    'model' => $model,
                                    'attributes' => [
                                        'id',
                                        'title',
                                        'country_id' => [
                                            'attribute' => 'country_id',
                                            'value' => function ($model) {
                                                return isset($model->country) ? $model->country->title : null;
                                            },
                                        ],
                                        'status' => [
                                            'attribute' => 'status',
                                            'value' => function ($model){
                                                $statuses =\app\modules\admin\models\City::getStatusList();
                                                return isset($statuses[$model->status]) ? $statuses[$model->status] : null;
                                            }
                                        ]
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
