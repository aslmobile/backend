<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\LangSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt("Языки приложения", [], 0);
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->mv->gt("Панель управления", [], 0),
    'url' => ['/admin'],
];
$this->params['breadcrumbs'][] = $this->title;

$yes_no = Yii::$app->params['yes_no'];

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
                        Yii::$app->mv->gt('{i} Добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false),
                        ['create'],
                        ['class' => 'btn btn-default btn-sm']
                    ); ?>
                    <?= Html::a(
                        Yii::$app->mv->gt('{i} Удалить', ['i' => Html::tag('i', '', ['class' => 'fa fa-fire'])], false),
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
                        'value' => function ($data) {
                            return Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $data->flag, '50x50', ['class' => 'img-circle width-1']);
                        },
                    ],
                    'local',
                    'name',
                    [
                        'attribute' => 'default',
                        'filter' => $yes_no,
                        'value' => function ($data) {
                            return ($data->default == 0) ? '' : '<i class="fa fa-check text-success"></i>';
                        },
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'url',
                        'format' => 'url',
                        'content' => function ($data) {
                            return Html::a($data->url, [
                                'update',
                                'id' => $data->id,
                            ]);
                        },
                    ],
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
                                        'pjax' => '0',
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
