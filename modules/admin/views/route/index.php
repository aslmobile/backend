<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\RouteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt(Yii::t('app', 'Маршруты'),[],false);
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
                        'onclick' => "window.location = '" . \yii\helpers\Url::toRoute("/admin/route/view/" . $key) . "'"
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
                    'title',
                    'status' => [
                        'attribute' => 'status',
                        'content' => function ($data) {
                            return key_exists($data->status, $data->statusList) ? $data->statusList[$data->status] : false;
                        },
                        'filter' => \app\modules\admin\models\Route::getStatusList(),
                    ],

                    'start_city_id' => [
                        'attribute' => 'start_city_id',
                        'content' => function ($data) {
                            $cities = \app\modules\admin\models\City::getCitiesList(true);
                            return key_exists($data->start_city_id, $cities) ? $cities[$data->start_city_id] : false;
                        },
                        'filter' => \app\modules\admin\models\City::getCitiesList(true),
                    ],
                    'end_city_id' => [
                        'attribute' => 'end_city_id',
                        'content' => function ($data) {
                            $cities = \app\modules\admin\models\City::getCitiesList(true);
                            return key_exists($data->end_city_id, $cities) ? $cities[$data->end_city_id] : false;
                        },
                        'filter' => \app\modules\admin\models\City::getCitiesList(true),
                    ],

                    'base_tariff',
                ],
            ]); ?>
        </div>
    </section>
</div>
