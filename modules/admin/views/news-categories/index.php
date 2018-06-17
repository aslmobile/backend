<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\NewsCategoriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Категории новостей',[],false);
$this->params['breadcrumbs'][] = $this->title;

$created_at = (!empty(Yii::$app->request->get('NewsCategoriesSearch')['created_at'])) ?Yii::$app->request->get('NewsCategoriesSearch')['created_at'] : null;
$updated_at = (!empty(Yii::$app->request->get('NewsCategoriesSearch')['updated_at'])) ?Yii::$app->request->get('NewsCategoriesSearch')['updated_at'] : null;
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
                    <?= Html::a(Yii::$app->mv->gt('{i} добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false), ['create'], ['class' => 'btn btn-default btn-sm']); ?>
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
                    'title',
                    [
                        'attribute' => 'status',
                        'filter' => \app\models\NewsCategories::getStatuses(),
                        'value' => function ($model, $index, $widget) {
                            return key_exists($model->status, $model->statuses)? $model->statuses[$model->status] : false;
                        },
                    ],
                    'sort',
                    'created_at' => [
                        'attribute' => 'created_at',
                        'filter' => DatePicker::widget([
                            'name' => 'NewsCategoriesSearch[created_at]',
                            'value' => $created_at,
                            'options' => [
                                'class' => "form-control",
                                'placeholder' => ''
                            ]
                        ]),
                        'format' => 'datetime'
                    ],
                    'updated_at' => [
                        'attribute' => 'updated_at',
                        'filter' => DatePicker::widget([
                            'name' => 'NewsCategoriesSearch[updated_at]',
                            'value' => $updated_at,
                            'options' => [
                                'class' => "form-control",
                                'placeholder' => ''
                            ]
                        ]),
                        'format' => 'datetime'
                    ],
                    // 'created_by',
                    // 'updated_by',
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
                                            'confirm' => Yii::$app->mv->gt('Вы уверены, что хотите удалить?', [], false),
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
