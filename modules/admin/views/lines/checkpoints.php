<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\RouteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Контрольные точки', [], false);
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
                    <?= Html::a(Yii::$app->mv->gt('{i} Добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false), ['create-checkpoint'], ['class' => 'btn btn-default btn-sm']); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'grid',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'role' => 'button',
                        'onclick' => "window.location = '" . \yii\helpers\Url::toRoute("/admin/lines/view-checkpoint/" . $key) . "'"
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
                    'title',
                    'status' => [
                        'attribute' => 'status',
                        'content' => function ($data) {
                            return key_exists($data->status, $data->statusList) ? $data->statusList[$data->status] : false;
                        },
                        'filter' => \app\modules\admin\models\Checkpoint::getStatusList(),
                    ],
                    'type' => [
                        'attribute' => 'type',
                        'content' => function ($data) {
                            return key_exists($data->type, $data->typesList) ? $data->typesList[$data->type] : false;
                        },
                        'filter' => \app\modules\admin\models\Checkpoint::getTypesList(),
                    ],
                    'route' => [
                        'attribute' => 'route',
                        'content' => function ($data) {
                            if ($data->getRouteModel()) return $data->routeModel->title;

                            return 'Route ID: ' . $data->route . '. Deleted.';
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'route',
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
                    ]
                ],
            ]); ?>
        </div>
    </section>
</div>
