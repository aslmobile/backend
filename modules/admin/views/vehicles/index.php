<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */

$this->title = Yii::$app->mv->gt('Автомобили', [], false);
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
                    <?= Html::a(Yii::$app->mv->gt('{i} Добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false), ['create'], ['class' => 'btn btn-default btn-sm']); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
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
//                        ['class' => 'yii\grid\CheckboxColumn', 'headerOptions' => ['style' => 'width: 50px;']],

                        'id' => [
                            'attribute' => 'id',
                            'headerOptions' => ['style' => 'width: 50px;'],
                            'filterInputOptions' => [
                                'class' => 'form-control',
                            ],
                        ],
                        'user_id' => [
                            'attribute' => 'user_id',
                            'content' => function ($data) {
                                return $data->user->fullName;
                            },
                            'filter' => Select2::widget([
                                'model' => $searchModel,
                                'theme' => Select2::THEME_DEFAULT,
                                'attribute' => 'user_id',
                                'hideSearch' => true,
                                'options' => [
                                    'placeholder' => Yii::$app->mv->gt('Найти пользователя', [], false)
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 1,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::toRoute(['/admin/user/select-users']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                    ],
                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                    'templateResult' => new JsExpression('function(user) { return user.text; }'),
                                    'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                                    'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/user/select-users']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                                ]
                            ]),
                        ],
                        'vehicle_type_id' => [
                            'attribute' => 'vehicle_type_id',
                            'content' => function ($data) {
                                $types = \app\modules\admin\models\VehicleType::getTypesList();
                                return key_exists($data->vehicle_type_id, $types) ? $types[$data->vehicle_type_id] : false;
                            },
                            'filter' => \app\modules\admin\models\VehicleType::getTypesList(),
                        ],
                        'vehicle_type_id' => [
                            'attribute' => 'vehicle_type_id',
                            'content' => function ($data) {
                                $types = \app\modules\admin\models\VehicleType::getTypesList();
                                return key_exists($data->vehicle_type_id, $types) ? $types[$data->vehicle_type_id] : false;
                            },
                            'filter' => \app\modules\admin\models\VehicleType::getTypesList(),
                        ],
                        'vehicle_brand_id' => [
                            'attribute' => 'vehicle_brand_id',
                            'content' => function ($data) {
                                $brands = \app\modules\admin\models\VehicleBrand::getBrandsList();
                                return key_exists($data->vehicle_brand_id, $brands) ? $brands[$data->vehicle_brand_id] : false;
                            },
                            'filter' => \app\modules\admin\models\VehicleBrand::getBrandsList(),
                        ],
                        'vehicle_model_id' => [
                            'attribute' => 'vehicle_model_id',
                            'content' => function ($data) {
                                $models = \app\modules\admin\models\VehicleModel::getModelsList();
                                return key_exists($data->vehicle_model_id, $models) ? $models[$data->vehicle_model_id] : false;
                            },
                            'filter' => \app\modules\admin\models\VehicleModel::getModelsList(),
                        ],
                        'seats' => [
                            'attribute' => 'seats',
                            'headerOptions' => ['style' => 'width: 60px;'],
                        ],
                        'status' => [
                            'attribute' => 'status',
                            'content' => function ($data) {
                                return key_exists($data->status, $data->statusList) ? $data->statusList[$data->status] : false;
                            },
                            'filter' => \app\modules\admin\models\Vehicles::getStatusList(),
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
        </div>
    </section>
</div>
