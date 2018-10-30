<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\VehiclesTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Типы автомобилей', [], false);
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
                    <?= Html::a(Yii::$app->mv->gt('{i} Добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false), ['create-type'], ['class' => 'btn btn-default btn-sm']); ?>
                    <?= Html::a(
                        Yii::$app->mv->gt('{i} Удалить выбранные', ['i' => Html::tag('i', '', ['class' => 'fa fa-fire'])], false),
                        [''],
                        [
                            'class' => 'btn btn-danger btn-sm',
                            'onclick' => "
                                    var keys = $('#grid').yiiGridView('getSelectedRows');
                                    if (keys!='') {
                                        if (confirm('" . Yii::$app->mv->gt('Are you sure you want to delete the selected items?', [], false) . "')) {
                                            $.ajax({
                                                type : 'POST',
                                                data: {keys : keys},
                                                success : function(data) {}
                                            });
                                        }
                                    }
                                    return false;
                                ",
                        ]
                    ); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'grid',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'role' => 'button',
                        'onclick' => "window.location = '" . \yii\helpers\Url::toRoute("/admin/vehicles/type/" . $key) . "'"
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
                    ['class' => 'yii\grid\CheckboxColumn', 'headerOptions' => ['style' => 'width: 50px;']],
                    ['attribute' => 'id', 'headerOptions' => ['style' => 'width: 50px;']],
                    'title',
                    'status' => [
                        'attribute' => 'status',
                        'filter' => \app\modules\admin\models\VehicleType::getStatusList(),
                        'value' => function ($model) {
                            $statuses = \app\modules\admin\models\VehicleType::getStatusList();
                            return isset($statuses[$model->status]) ? $statuses[$model->status] : null;
                        }
                    ]
                ],
            ]); ?>
        </div>
    </section>
</div>
