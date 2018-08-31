<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;
use yii\web\JsExpression;

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
                <div class="box-tools pull-right">
                    <?= Html::a(Yii::$app->mv->gt('{i} Добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false), ['create'], ['class' => 'btn btn-default btn-sm']); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'grid',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'role' => 'button',
                        'onclick' => "window.location = '" . \yii\helpers\Url::toRoute("/admin/lines/update/" . $key) . "'"
                    ];
                },
                'layout' => "
                    <div class='box-body' style='display: block;'><div class='col-sm-12 right-text'>{summary}</div><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>
                ",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    'driver_id' => [
                        'attribute' => 'driver_id',
                        'value' => function ($model) {
                            return $model->driver->fullName . '<br /><a href="tel:+' . $model->driver->phone . '">+' . $model->driver->phone . '</a>';
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'driver_id',
                            'hideSearch' => true,
                            'options' => [
                                'placeholder' => Yii::$app->mv->gt('Найти водителя', [], false)
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
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'driver_id',
                        'label'     => Yii::t('app', "Рейтинг"),
                        'value'     => function ($model) {
                            return $model->driver->rating;
                        }
                    ],
                    [
                        'attribute' => 'driver_id',
                        'label'     => Yii::t('app', "В сети"),
                        'value'     => function ($model) {
                            return $model->driver->online ? '<span class="fa fa-circle text-green"></span> <small class="text-uppercase text-green">' . Yii::t('app', "В сети") . '</small>' : '<span class="fa fa-circle text-red"></span> <small class="text-uppercase text-red">' . Yii::t('app', "Оффлайн") . '</small>';
                        },
                        'format'    => 'html'
                    ],
                    'vehicle_id' => [
                        'attribute' => 'vehicle_id',
                        'value'     => function ($model) {
                            return '<span>' . $model->vehicle->license_plate . '</span> &nbsp; ' . $model->vehicle->vehicleName;
                        },
                        'format'    => 'html'
                    ],
                    'freeseats',
                    'route_id' => [
                        'attribute' => 'route_id',
                        'value'     => function ($model) {
                            return $model->route->title;
                        },
                        'format'    => 'html'
                    ],
                    'route_id' => [
                        'attribute' => 'route_id',
                        'content' => function ($model) {
                            return ($model->route) ? $model->route->title : Yii::t('app', 'Маршрут удален');
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'route_id',
                            'hideSearch' => true,
                            'options' => [
                                'placeholder' => Yii::$app->mv->gt('Найти маршрут', [], false)
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 1,
                                'ajax' => [
                                    'url' => \yii\helpers\Url::toRoute(['/admin/lines/select-route']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(user) { return user.text; }'),
                                'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                                'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/lines/select-route']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                            ]
                        ]),
                    ],
                    'created_at' => [
                        'attribute' => 'created_at',
                        'label' => Yii::t('app', "Стал на линию"),
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime($model->created_at);
                        }
                    ]
                ],
            ]); ?>
        </div>
    </section>
</div>
