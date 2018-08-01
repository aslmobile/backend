<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\TripSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Пассажиры в очереди',[],false);
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
                        Yii::$app->mv->gt('{i} Добавить',['i'=>Html::tag('i','',['class'=>'fa fa-plus'])],false),
                        ['create'],
                        ['class' => 'btn btn-default btn-sm']
                    ); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'grid',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'role' => 'button',
                        'onclick' => "window.location = '" . \yii\helpers\Url::toRoute("/admin/trips/view/" . $key) . "'"
                    ];
                },
                'layout'=>"
                    <div class='box-body' style='display: block;'><div class='col-sm-12 right-text'>{summary}</div><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>
                ",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    'user_id' => [
                        'attribute' => 'user_id',
                        'content' => function ($data) {
                            return ($data->user) ? $data->user->fullName . '<br /><a href="tel:+' . $data->user->phone . '">+' . $data->user->phone . '</a>' : Yii::t('app', "Удален");
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
                            return key_exists($data->vehicle_type_id, $data->vehicleTypeList) ? $data->vehicleTypeList[$data->vehicle_type_id] : false;
                        },
                        'filter' => \app\models\Trip::getVehicleTypeList(),
                    ],
                    'startpoint_id' => [
                        'attribute' => 'startpoint_id',
                        'content' => function ($data) {
                            return $data->startpoint->title;
                        },
                        'filter' => false,
                    ],
                    'status' => [
                        'attribute' => 'status',
                        'content' => function ($data) {
                            return key_exists($data->status, $data->statusList) ? $data->statusList[$data->status] : false;
                        },
                        'filter' => \app\models\Trip::getStatusList(),
                    ],
                    'created_at' => [
                        'attribute' => 'created_at',
                        'value' => function ($module)
                        {
                            return Yii::$app->formatter->asDateTime($module->created_at);
                        },
                        'format' => 'html'
                    ]
                ],
            ]); ?>
        </div>
    </section>
</div>
