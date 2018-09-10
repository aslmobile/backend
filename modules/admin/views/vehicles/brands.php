<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\VehiclesTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Бренды автомобилей', [], false);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h2><?= $this->title; ?></h2>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools pull-right">
                    <?= Html::a(Yii::$app->mv->gt('{i} Добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false), ['create-brand'], ['class' => 'btn btn-default btn-sm']); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'grid',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'role' => 'button',
                        'onclick' => "window.location = '" . \yii\helpers\Url::toRoute("/admin/vehicles/brand/" . $key) . "'"
                    ];
                },
                'layout' => "
                    <div class='box-body' style='display: block;'><div class='col-sm-12 right-text'>{summary}</div><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>
                ",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    'title',
                    'status' => [
                        'attribute' => 'status',
                        'filter' => \app\modules\admin\models\VehicleBrand::getStatusList(),
                        'value' => function ($model){
                            $statuses =\app\modules\admin\models\VehicleBrand::getStatusList();
                            return isset($statuses[$model->status]) ? $statuses[$model->status] : null;
                        }
                    ]
                ],
            ]); ?>
        </div>
    </section>
</div>
