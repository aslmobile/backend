<?php

use app\modules\admin\models\BotTrip;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\BotTripSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Поездки', [], false);
$this->params['breadcrumbs'][] = $this->title;

$statuses = BotTrip::getStatusList();

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
                        Yii::$app->mv->gt('{i} Удалить выбранные', ['i' => Html::tag('i', '', ['class' => 'fa fa-fire'])], false),
                        [''],
                        [
                            'class' => 'btn btn-danger btn-sm',
                            'onclick' => "
                                var keys = $('#w0').yiiGridView('getSelectedRows');
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
                'pjax' => true,
                'pjaxSettings' => [
                    'neverTimeout' => true,
                    'options' => [
                        'enablePushState' => false,
                        'options' => ['tag' => 'span']
                    ],
                ],
                'layout' => "
                    <div class='box-body' style='display: block;'><div class='col-sm-12 right-text'>{summary}</div><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>
                ",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\CheckboxColumn'],
                    'id',
                    [
                        'attribute' => 'user_id',
                        'content' => function ($data) {
                            return isset($data->user) ?
                                $data->user->fullName . '<br /><a href="tel:+' . $data->user->phone . '">+' . $data->user->phone . '</a>' :
                                Yii::t('app', "Удален");
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [null => 'Все'] + \yii\helpers\ArrayHelper::map(
                                    \app\modules\admin\models\User::find()
                                        ->select(['id', 'name' => 'CONCAT(phone, \' \',first_name,\' \',second_name)'])
                                        ->where(['=', 'type', \app\modules\admin\models\User::TYPE_PASSENGER])->asArray()->all(),
                                    'id', 'name'),
                        ],
                        'headerOptions' => ['style' => ['width' => '200px']]
                    ],
                    [
                        'attribute' => 'route_id',
                        'content' => function ($data) {
                            return isset($data->route) ?
                                $data->route->title :
                                Yii::t('app', "Удален");
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [null => 'Все'] + \yii\helpers\ArrayHelper::map(
                                    \app\modules\admin\models\Route::find()
                                        ->where(['status' => \app\models\Route::STATUS_ACTIVE])->asArray()->all(),
                                    'id', 'title'),
                        ],
                        'headerOptions' => ['style' => ['width' => '200px']]
                    ],
                    [
                        'attribute' => 'vehicle_type_id',
                        'content' => function ($data) {
                            return key_exists($data->vehicle_type_id, $data->vehicleTypeList) ? $data->vehicleTypeList[$data->vehicle_type_id] : false;
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [null => 'Все'] + \app\models\Trip::getVehicleTypeList(),
                        ],
                        'headerOptions' => ['style' => ['width' => '200px']]
                    ],
                    [
                        'attribute' => 'startpoint_id',
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [null => 'Все'] + \app\models\Trip::getAllRoutePoints(),
                        ],
                        'headerOptions' => ['style' => 'width: 150px;'],
                        'value' => function ($model) {
                            return isset($model->startpoint) ? $model->startpoint->title : null;
                        },
                        'refreshGrid' => true,
                        'class' => '\kartik\grid\EditableColumn',
                        'editableOptions' => function ($model, $key, $index) {
                            return [
                                'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                                'data' => $model->routePoints,
                                'format' => \kartik\editable\Editable::FORMAT_BUTTON,
                                'placement' => \kartik\popover\PopoverX::ALIGN_BOTTOM,
                                'formOptions' => [
                                    'action' => \yii\helpers\Url::toRoute(['startpoint_id']),
                                ],
                            ];
                        }
                    ],
                    [
                        'attribute' => 'line_id',
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'data' => [null => 'Все'] + \app\models\Trip::getAllLines(),
                        ],
                        'headerOptions' => ['style' => 'width: 250px;'],
                        'content' => function ($model) {
                            $title = isset($model->line) && isset($model->line->driver) ? $model->line->driver->fullName . ' ' . $model->route->title : '';
                            $seats = isset($model->line) ? '<br>Свободных мест: ' . $model->line->freeseats : '';
                            return $title . $seats;
                        },
                        'refreshGrid' => true,
                        'class' => '\kartik\grid\EditableColumn',
                        'editableOptions' => function ($model, $key, $index) {
                            return [
                                'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                                'data' => [0 => 'Не установлено'] + $model->lines,
                                'format' => \kartik\editable\Editable::FORMAT_BUTTON,
                                'placement' => \kartik\popover\PopoverX::ALIGN_BOTTOM,
                                'formOptions' => [
                                    'action' => \yii\helpers\Url::toRoute(['line_id']),
                                ],
                            ];
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => $statuses,
                        'headerOptions' => ['style' => 'width: 150px;'],
                        'value' => function ($model) {
                            return isset($model->statusList[$model->status])
                                ? $model->statusList[$model->status] : null;
                        },
                        'refreshGrid' => true,
                        'class' => '\kartik\grid\EditableColumn',
                        'editableOptions' => function ($model, $key, $index) use ($statuses) {
                            return [
                                'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                                'data' => $statuses,
                                'format' => \kartik\editable\Editable::FORMAT_BUTTON,
                                'placement' => \kartik\popover\PopoverX::ALIGN_BOTTOM,
                                'formOptions' => [
                                    'action' => \yii\helpers\Url::toRoute(['status']),
                                ],
                            ];
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($module) {
                            return Yii::$app->formatter->asDateTime($module->created_at);
                        },
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
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {delete}',
                        'contentOptions' => ['style' => ['width' => '90px']],
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a(
                                    '<button type="button" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></button>',
                                    $url,
                                    [
                                        'data' => [
                                            'pjax' => '0'
                                        ]
                                    ]
                                );
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    '<button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>',
                                    $url,
                                    [
                                        'data' => [
                                            'confirm' => Yii::$app->mv->gt('Вы уверены, что хотите удалить это ?', [], false),
                                            'method' => 'post',
                                            'pjax' => '0'
                                        ]
                                    ]
                                );
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </section>
</div>
