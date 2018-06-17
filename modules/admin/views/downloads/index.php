<?php

use app\models\Category;
use app\modules\admin\models\Downloads;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\DownloadsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Downloads';
$this->params['breadcrumbs'][] = $this->title;

$created_at =(!empty(Yii::$app->request->get('CategorySearch')['created_at'])) ? Yii::$app->request->get('CategorySearch')['created_at'] : null;
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
                        Yii::$app->mv->gt('{i} new', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false),
                        ['create'],
                        ['class' => 'btn btn-default btn-sm']
                    ); ?>
                    <?= Html::a(
                        Yii::$app->mv->gt('{i} delete selected', ['i' => Html::tag('i', '', ['class' => 'fa fa-fire'])], false),
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

                    'id',
                    'title',
                    [
                        'attribute' => 'category_id',
                        'filter' => ArrayHelper::map(Category::find()->all(), 'id', 'title'),
                        'value' => function ($model) {
                            if (isset($model->category)) {
                                return $model->category->title;
                            }


                        }
                    ],
                    'source',
                    'status' => [
                        'attribute' => 'status',
                        'content' => function ($data) {
                            return key_exists($data->status, $data->statuses) ? $data->statuses[$data->status] : false;
                        },
                        'filter' => Downloads::getStatuses(),
                        'filterInputOptions' => [
                            'class' => 'form-control',
                            'placeholder' => 'Select status'
                        ],
                    ],
                    'created_at' => [
                        'attribute' => 'created_at',
                        'filter' => DatePicker::widget([
                            'name' => 'DownloadsSearch[created_at]',
                            'value' => $created_at,
                            'options' => ['class' => 'form-control', 'placeholder' => 'Select date']
                        ]),
                        'format' => 'dateTime'
                    ],
//                    'updated_at' => [
//                        'attribute' => 'updated_at',
//                        'filter' => DatePicker::widget([
//                            'name' => 'ProductsSearch[updated_at]',
//                            'value' => $updated_at,
//                            'options' => ['class' => 'form-control', 'placeholder' => 'Select date']
//                        ]),
//                        'format' => 'dateTime'
//                    ],
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
                                    ['data' => [
                                        'confirm' => Yii::$app->mv->gt('Are you sure you want to delete this item?', [], false),
                                        'method' => 'post',
                                        'pjax' => '0'
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
