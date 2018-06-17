<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\TuneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Tunes',[],false);
$this->params['breadcrumbs'][] = $this->title;

$types = ['textInput', 'image', 'file', 'textarea', 'textareaMce', 'date'];
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
                        Yii::$app->mv->gt('{i} новая',['i'=>Html::tag('i','',['class'=>'fa fa-plus'])],false),
                        ['create'],
                        ['class' => 'btn btn-default btn-sm']
                    ); ?>
                    <?= Html::a(
                        Yii::$app->mv->gt('{i} удалить выбранные',['i'=>Html::tag('i','',['class'=>'fa fa-fire'])],false),
                        [''],
                        [
                            'class' => 'btn btn-danger btn-sm',
                            'onclick'=>"
								var keys = $('#grid').yiiGridView('getSelectedRows');
								if (keys!='') {
									if (confirm('".Yii::$app->mv->gt('Вы уверены, что хотите удалить выбранные элементы?',[],false)."')) {
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
										<div class='dataTables_info'>{summary}</div>
										<div class='card'>
											<div class='card-body no-padding'>
												<div class='table-responsive no-margin'>{items}</div>
											</div>
										</div>
										<div class='dataTables_paginate paging_simple_numbers'>{pager}</div>
									",
                'tableOptions' => [
                    'class' => 'table table-striped no-margin table-hover',
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    ['class' => 'yii\grid\CheckboxColumn'],
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width: 50px;']
                    ],
                    [
                        'attribute' => 'widget',
                        'filter' => $types,
                        'value' => function ($model, $index, $widget) use ($types) {
                            return $types[$model->widget];
                        },
                    ],
                    'type',
                    //'val',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'width: 125px;'],
                        'template' => '{view} {update} {delete}',
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
                                        'confirm' => Yii::$app->mv->gt('Удалить?', [], false),
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
