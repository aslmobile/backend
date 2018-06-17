<?php

use kartik\date\DatePicker;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\GalleryContentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Galleries content', [], false);
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
                        Yii::$app->mv->gt('{i} новая', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false),
                        ['create'],
                        ['class' => 'btn btn-default btn-sm']
                    ); ?>
                    <?= Html::a(
                        Yii::$app->mv->gt('{i} удалить выбранные', ['i' => Html::tag('i', '', ['class' => 'fa fa-fire'])], false),
                        [''],
                        [
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
                        ]
                    ); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'grid',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    $class_color = [
                        0 => 'success',
                        1 => 'danger',
                        2 => 'info',
                    ];

                    return [
                        'class' => (key_exists($model->status, $class_color)) ? $class_color[$model->status] : '',
                    ];
                },
                'layout' => "
                    <div class='dataTables_info'>{summary}</div>
                    <div class='box-body' style='display: block;'><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>
                ",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],
                    ['class' => 'yii\grid\CheckboxColumn'],
                    'id' => [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width: 50px;'],
                    ],
                    [
                        'filter' => false,
                        'format' => 'html',
                        'contentOptions' => ['class' => 'content_preview'],
                        'content' => function ($model) {
                            if ($model->type == 1) {
                                return \wbraganca\videojs\VideoJsWidget::widget([
                                    'options' => [
                                        'class' => 'video-js vjs-default-skin vjs-big-play-centered',
                                        'controls' => true,
                                        'preload' => 'auto',
                                    ],
                                    'tags' => [
                                        'source' => [
                                            ['src' => Yii::getAlias('@web') . $model->path, 'type' => 'video/' . $model->ext],
                                        ],
                                    ],
                                ]);
                            } else {
                                return \branchonline\lightbox\Lightbox::widget([
                                    'files' => [
                                        [
                                            'thumb' => Yii::getAlias('@web') . $model->path,
                                            'original' => Yii::getAlias('@web') . $model->path,
                                            'title' => 'optional title',
                                        ],
                                    ],
                                ]);
                            }
                        },
                    ],
                    [
                        'attribute' => 'title',
                        'format' => 'url',
                        'content' => function ($data) {
                            return Html::a($data->title, ['update', 'id' => $data->id]);
                        },
                    ],
                    [
                        'attribute' => 'gallery',
                        'filter' => ArrayHelper::map(\app\modules\admin\models\Gallery::find()->asArray()->all(), 'id', 'title'),
                        'headerOptions' => ['style' => 'min-width: 100px;'],
                        'value' => function ($model) {
                            $galleries = ArrayHelper::map(\app\modules\admin\models\Gallery::find()->asArray()->all(), 'id', 'title');

                            return isset($galleries[$model->gallery_id]) ? $galleries[$model->gallery_id] : null;
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => Yii::$app->params['content_status'],
                        'headerOptions' => ['style' => 'min-width: 100px;'],
                        'value' => function ($model) {
                            return isset(Yii::$app->params['content_status'][$model->status]) ? (Yii::$app->params['content_status'][$model->status]) : '';
                        },
                    ],
                    [
                        'attribute' => 'type',
                        'filter' => Yii::$app->params['content_type'],
                        'headerOptions' => ['style' => 'min-width: 100px;'],
                        'value' => function ($model) {
                            return Yii::$app->params['content_type'][$model->type];
                        },
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => 'created_at',
                        'format' => 'datetime',
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'created_at',
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy',
                            ],
                        ]),

                    ],
                    [
                        'attribute' => 'created_by',
                        'filter' => ArrayHelper::map(\app\modules\admin\models\User::find()->asArray()->all(), 'id', 'last_name'),
                        'filterInputOptions' => [
                            'class' => 'form-control',
                        ],
                        'headerOptions' => ['style' => 'min-width: 150px;'],
                        'value' => function ($model) {
                            $user = \app\models\User::findOne($model->created_by);

                            return $user ? $user->last_name : '';
                        },
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => 'updated_at',
                        'format' => 'datetime',
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'updated_at',
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy',
                            ],
                        ]),

                    ],
                    [
                        'attribute' => 'updated_by',
                        'filter' => ArrayHelper::map(\app\modules\admin\models\User::find()->asArray()->all(), 'id', 'last_name'),
                        'filterInputOptions' => [
                            'class' => 'form-control',
                        ],
                        'headerOptions' => ['style' => 'min-width: 150px;'],
                        'value' => function ($model) {
                            $user = \app\models\User::findOne($model->updated_by);

                            return $user ? $user->last_name : '';
                        },
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'headerOptions' => ['style' => 'min-width: 120px;'],
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<button style="margin-bottom: 5px" type="button" class="btn btn-info btn-sm"><i class="fa fa-search"></i></button>', $url);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<button style="margin-bottom: 5px" type="button" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></button>', $url);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    '<button style="margin-bottom: 5px" type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>',
                                    $url,
                                    ['data' => [
                                        'confirm' => Yii::$app->mv->gt('Are you sure you want to delete this item?', [], false),
                                        'method' => 'post',
                                        'pjax' => true,
                                    ]]
                                );
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </section>
</div>
