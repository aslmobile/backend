<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\TripSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Trips',[],false);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h2></h2>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

                <div class="box-tools pull-right">
                    <?= Html::a(
                        Yii::$app->mv->gt('{i} new',['i'=>Html::tag('i','',['class'=>'fa fa-plus'])],false),
                        ['create'],
                        ['class' => 'btn btn-default btn-sm']
                    ); ?>
                    <?= Html::a(
                        Yii::$app->mv->gt('{i} delete selected',['i'=>Html::tag('i','',['class'=>'fa fa-fire'])],false),
                        [''],
                        [
                            'class' => 'btn btn-danger btn-sm',
                            'onclick'=>"
								var keys = $('#grid').yiiGridView('getSelectedRows');
								if (keys!='') {
									if (confirm('".Yii::$app->mv->gt('Are you sure you want to delete the selected items?',[],false)."')) {
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
                'layout'=>"
                    <div class='box-body' style='display: block;'><div class='col-sm-12 right-text'>{summary}</div><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>
                ",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $searchModel,
        'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    ['class' => 'yii\grid\CheckboxColumn'],

                                'id',
            'created_at',
            'updated_at',
            'status',
            'user_id',
            // 'amount',
            // 'tariff',
            // 'cancel_reason',
            // 'passenger_description',
            // 'created_by',
            // 'updated_by',
            // 'currency',
            // 'payment_type',
            // 'passenger_rating',
            // 'startpoint_id',
            // 'route_id',
            // 'seats',
            // 'driver_comment',
            // 'endpoint_id',
            // 'payment_status',
            // 'vehicle_type_id',
            // 'luggage_unique_id',
            // 'line_id',
            // 'passenger_comment',
            // 'driver_rating',
            // 'vehicle_id',
            // 'driver_id',
            // 'need_taxi',
            // 'taxi_status',
            // 'taxi_cancel_reason',
            // 'taxi_address',
            // 'taxi_time:datetime',
            // 'scheduled',
            // 'schedule_id',
            // 'start_time:datetime',
            // 'finish_time:datetime',
            // 'driver_description',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<button type="button" class="btn btn-info btn-sm"><i class="fa fa-search"></i></button>', $url);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<button type="button" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></button>', $url);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    '<button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>',
                                    $url,
                                    ['data'=>[
                                        'confirm'=>Yii::$app->mv->gt('Are you sure you want to delete this item?',[],false),
                                        'method'=>'post',
                                        'pjax'=>'0'
                                    ]]
                                );
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </section>
</div>
