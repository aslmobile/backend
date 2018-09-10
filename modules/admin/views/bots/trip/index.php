<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\BotTripSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Поездки', [], false);
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
                        'onclick' => "window.location = '" . \yii\helpers\Url::toRoute("/admin/bot-trip/view/" . $key) . "'"
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
                            return isset($data->user) ?
                                $data->user->fullName . '<br /><a href="tel:+' . $data->user->phone . '">+' . $data->user->phone . '</a>' :
                                Yii::t('app', "Удален");
                        },
                        'filter' => Select2::widget([
                            'model' => $searchModel,
                            'data' => \yii\helpers\ArrayHelper::map(
                                \app\modules\admin\models\User::find()
                                    ->select(['id', 'name' => 'CONCAT(phone, \' \',first_name,\' \',second_name)'])
                                    ->where(['=', 'type', \app\modules\admin\models\User::TYPE_PASSENGER])->asArray()->all(),
                                'id', 'name'),
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'user_id',
                            'options' => [
                                'placeholder' => Yii::$app->mv->gt('Пользователя', [], false)
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
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
                        'value' => function ($module) {
                            return Yii::$app->formatter->asDateTime($module->created_at);
                        },
                        'format' => 'html'
                    ]
                ],
            ]); ?>
        </div>
    </section>
</div>
