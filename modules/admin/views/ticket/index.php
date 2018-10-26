<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\TicketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Вывод средств', [], false);
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
<!--                    --><?//= Html::a(
//                        Yii::$app->mv->gt('{i} Новая', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false),
//                        ['create'],
//                        ['class' => 'btn btn-default btn-sm']
//                    ); ?>
                    <!--                    --><? //= Html::a(
                    //                        Yii::$app->mv->gt('{i} Удалить выбранные', ['i' => Html::tag('i', '', ['class' => 'fa fa-fire'])], false),
                    //                        [''],
                    //                        [
                    //                            'class' => 'btn btn-danger btn-sm',
                    //                            'onclick' => "
                    //								var keys = $('#grid').yiiGridView('getSelectedRows');
                    //								if (keys!='') {
                    //									if (confirm('" . Yii::$app->mv->gt('Are you sure you want to delete the selected items?', [], false) . "')) {
                    //										$.ajax({
                    //											type : 'POST',
                    //											data: {keys : keys},
                    //											success : function(data) {}
                    //										});
                    //									}
                    //								}
                    //								return false;
                    //							",
                    //                        ]
                    //                    ); ?>
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
                    ['attribute' => 'id', 'headerOptions' => ['style' => ['width' => '50px']]],
                    [
                        'attribute' => 'user_id',
                        'content' => function ($data) {
                            return !empty($data->user) ?
                                $data->user->fullName :
                                Yii::t('app', "Удален");
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [null => 'Все'] + \yii\helpers\ArrayHelper::map(
                                    \app\models\User::find()
                                        ->select(['id', 'name' => 'CONCAT(phone, \' \',first_name,\' \',second_name)'])
                                        ->where(['=', 'type', \app\modules\admin\models\User::TYPE_DRIVER])->asArray()->all(),
                                    'id', 'name'),
                        ],
                        'headerOptions' => ['style' => ['min-width' => '200px']]
                    ],
                    [
                        'attribute' => 'status',
                        'content' => function ($model) {
                            return $model->statusLabel;
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [null => 'Все'] + \app\models\Ticket::statusLabels(),
                        ],
                        'headerOptions' => ['style' => ['min-width' => '200px']]
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($module) {
                            return Yii::$app->formatter->asDateTime($module->created_at);
                        },
                        'format' => 'raw',
                        'headerOptions' => ['style' => 'width: 230px;'],
                        'filterType' => GridView::FILTER_DATETIME,
                        'filterWidgetOptions' => [
                            'model' => $searchModel,
                            'attribute' => 'created_at',
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'todayBtn' => true,
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy hh:ii',
                                'minuteStep' => 1,
                            ],
                        ],
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function ($module) {
                            return Yii::$app->formatter->asDateTime($module->created_at);
                        },
                        'format' => 'raw',
                        'headerOptions' => ['style' => 'width: 230px;'],
                        'filterType' => GridView::FILTER_DATETIME,
                        'filterWidgetOptions' => [
                            'model' => $searchModel,
                            'attribute' => 'updated_at',
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'todayBtn' => true,
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy hh:ii',
                                'minuteStep' => 1,
                            ],
                        ],
                    ],
                    [
                        'attribute' => 'updated_by',
                        'content' => function ($data) {
                            return !empty($data->updated) ?
                                $data->updated->fullName :
                                Yii::t('app', "Удален");
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [null => 'Все'] + \yii\helpers\ArrayHelper::map(
                                    \app\models\User::find()
                                        ->select(['id', 'name' => 'CONCAT(phone, \' \',first_name,\' \',second_name)'])
                                        ->where(['type' => [\app\models\User::TYPE_ADMIN, \app\models\User::TYPE_MANAGER]])
                                        ->asArray()->all(),
                                    'id', 'name'),
                        ],
                        'headerOptions' => ['style' => ['min-width' => '200px']]
                    ],
                    'amount',
                    // 'transaction_id',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<button type="button" class="btn btn-info btn-sm"><i class="fa fa-search"></i></button>', $url);
                            },
                            'update' => function ($url, $model) {
                                if ($model->status == \app\models\Ticket::STATUS_PAYED) return '';
                                return Html::a('<button type="button" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></button>', $url);
                            },
//                            'delete' => function ($url, $model) {
//                                return Html::a(
//                                    '<button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>',
//                                    $url,
//                                    ['data' => [
//                                        'confirm' => Yii::$app->mv->gt('Are you sure you want to delete this item?', [], false),
//                                        'method' => 'post',
//                                        'pjax' => '0'
//                                    ]]
//                                );
//                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </section>
</div>
