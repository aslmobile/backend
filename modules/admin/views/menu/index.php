<?php

use app\models\Menu;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Меню', [], false);
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
                    <?= Html::a(Yii::$app->mv->gt('{i} новый', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false), ['create'], ['class' => 'btn btn-default btn-sm']); ?>
                    <?= Html::a(Yii::$app->mv->gt('{i} удалить выбранные', ['i' => Html::tag('i', '', ['class' => 'fa fa-fire'])], false), [''], [
                        'class' => 'btn btn-danger btn-sm',
                        'onclick' => "
								var keys = $('#grid').yiiGridView('getSelectedRows');
								if (keys!='') {
									if (confirm('" . Yii::$app->mv->gt('Вы действительно хотите удальть выбранное?', [], false) . "')) {
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
                    //['class' => 'yii\grid\SerialColumn'],
                    ['class' => 'yii\grid\CheckboxColumn'],

                    [
                        'attribute' => 'id',
                        'headerOptions' => ['style' => 'width: 50px;'],
                        'filterInputOptions' => [
                            'class'       => 'form-control',
                        ],
                    ],
                    [
                        'attribute' => 'url',
                        'content' => function($model){
                            return Html::a($model->url,$model->url);
                        }
                    ],
                    'name',
                    [
                        'attribute' => 'parent_id',
                        'filter' => false,
                        'format' => 'html',
                        'content' => function ($data) {
                            $arr = ArrayHelper::merge([0 => '(Не выбрано)'], ArrayHelper::map(Menu::findAll(['id' => $data->parent_id]), 'id', 'name'));
                            if ($data->parent_id && isset($arr[$data->parent_id])) {
                                return $arr[$data->parent_id];
                            } else {
                                return Yii::$app->mv->gt('(not set)', [], false);
                            }
                        }
                    ],
                    [
                        'attribute' => 'sort_order',
                        'headerOptions' => ['style' => 'width: 50px;'],
                        'filterInputOptions' => [
                            'class'       => 'form-control',
                        ],
                    ],
                    [
                        'attribute' => 'visible_type',
                        'filter' => false,
                        'format' => 'html',
                        'content' => function ($data) {
                            $arr = Menu::getVisibilityTypes();
                            if ($data->visible_type && isset($arr[$data->visible_type])) {
                                return $arr[$data->visible_type];
                            } else {
                                return Yii::$app->mv->gt('(not set)', [], false);
                            }
                        }
                    ],
                    // 'visible_type',
                    // 'data_type',
                    // 'depth',
                    // 'group',
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
