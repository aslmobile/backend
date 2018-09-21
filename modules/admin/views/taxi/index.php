<?php

use kartik\select2\Select2;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\TaxiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Заказы такси', [], false);
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
                        Yii::$app->mv->gt('{i} Добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false),
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
                        'onclick' => "window.location = '" . \yii\helpers\Url::toRoute("/admin/taxi/view/" . $key) . "'"
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
                    'user_id' => [
                        'attribute' => 'user_id',
                        'content' => function ($data) {
                            return ($data->user) ? $data->user->fullName : '';
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
                                'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("' . \yii\helpers\Url::toRoute(['/admin/user/select-users']) . '", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                            ]
                        ]),
                    ],
                    'status' => [
                        'attribute' => 'status',
                        'content' => function ($data) {
                            $statuses = \app\models\Taxi::getStatusList();
                            return key_exists($data->status, $statuses) ? $statuses[$data->status] : false;
                        },
                        'filter' => \app\models\Taxi::getStatusList(),
                    ],
                    'address',
                    'checkpoint' => [
                        'attribute' => 'checkpoint',
                        'content' => function ($data) {
                            $checkpoint = \app\models\Checkpoint::findOne($data->checkpoint);
                            return !empty($checkpoint) ? $checkpoint->title : null;
                        },
                    ]
                ],
            ]); ?>
        </div>
    </section>
</div>
