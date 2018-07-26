<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\LineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Маршруты', [], false);
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
                    <?= Html::a(Yii::$app->mv->gt('{i} новая', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false), ['create'], ['class' => 'btn btn-default btn-sm']); ?>
                    <?= Html::a(Yii::$app->mv->gt('{i} удалить выбранные', ['i' => Html::tag('i', '', ['class' => 'fa fa-fire'])], false), [''], [
                        'class' => 'btn btn-danger btn-sm',
                        'onclick' => "
								var keys = $('#grid').yiiGridView('getSelectedRows');
								if (keys!='') {
									if (confirm('" . Yii::$app->mv->gt('Вы уверены, что хотите удалить выбранные элементы?', [], false) . "')) {
										$.ajax({
											type : 'POST',
											data: {keys : keys},
											success : function(data) {}
										});
									}
								}
								return false;
							",
                    ]); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'grid',
                'layout' => "
                    <div class='box-body' style='display: block;'><div class='col-sm-12 right-text'>{summary}</div><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>
                ",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'headerOptions' => ['style' => 'width: 50px;'],
                    ],

                    'id' => [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width: 50px;'],
                        'filterInputOptions' => [
                            'class' => 'form-control',
                        ],
                    ],
                    'driver_id' => [
                        'attribute' => 'driver_id',
                        'value'     => function ($model) {
                            return '<a href="tel:+' . $model->driver->phone . '">+' . $model->driver->phone . '</a> &nbsp; ' . $model->driver->fullName;
                        },
                        'format'    => 'html'
                    ],
                    [
                        'attribute' => 'driver_id',
                        'label'     => Yii::t('app', "Рейтинг"),
                        'value'     => function ($model) {
                            return $model->driver->rating;
                        }
                    ],
                    [
                        'attribute' => 'driver_id',
                        'label'     => Yii::t('app', "В сети"),
                        'value'     => function ($model) {
                            return $model->driver->online ? '<span class="fa fa-circle text-green"></span> <small class="text-uppercase text-green">' . Yii::t('app', "В сети") . '</small>' : '<span class="fa fa-circle text-red"></span> <small class="text-uppercase text-red">' . Yii::t('app', "Оффлайн") . '</small>';
                        },
                        'format'    => 'html'
                    ],
                    'vehicle_id' => [
                        'attribute' => 'vehicle_id',
                        'value'     => function ($model) {
                            return '<span>' . $model->vehicle->license_plate . '</span> &nbsp; ' . $model->vehicle->vehicleName;
                        },
                        'format'    => 'html'
                    ],
                    'status' => [
                        'attribute' => 'status',
                        'content' => function ($data) {
                            return key_exists($data->status, $data->statusList) ? $data->statusList[$data->status] : false;
                        },
                        'filter' => \app\modules\admin\models\Line::getStatusList(),
                    ],
                    'freeseats',
                    'created_at' => [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime($model->created_at);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'width: 125px;'],
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<button type="button" class="btn btn-info btn-sm"><i class="fa fa-search"></i></button>', $url);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<button type="button" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></button>', $url);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>', $url, [
                                    'data' => [
                                        'confirm' => Yii::$app->mv->gt('Are you sure you want to delete this item?', [], false),
                                        'method' => 'post',
                                        'pjax' => '0'
                                    ]
                                ]);
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </section>
</div>
