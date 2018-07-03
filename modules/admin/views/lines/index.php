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
