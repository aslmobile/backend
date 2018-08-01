<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Checkpoint */

$this->title = (strlen($model->title) > 3) ? $model->title : $model->id;
$this->params['breadcrumbs'][] = [
    'label' => 'Чекпоинты',
    'url' => ['routes']
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= Yii::$app->mv->gt('Инфомрация', [], false); ?></h3>
                        <div class="box-tools">
                            <?= Html::a('Редактировать', [
                                'update-checkpoint',
                                'id' => $model->id
                            ], ['class' => 'btn btn-sm btn-primary']) ?>
                            <?= Html::a('Удалить', [
                                'delete-checkpoint',
                                'id' => $model->id
                            ], [
                                'class' => 'btn btn-sm btn-danger',
                                'data' => [
                                    'confirm' => 'Вы действительно хотите удалить пользователя?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'attribute' => 'status',
                                    'value' => key_exists($model->status, $model->statusList)? $model->statusList[$model->status] : null,
                                ],
                                'title',
                                'weight',
                                [
                                    'attribute' => 'route',
                                    'value' => $model->getRouteModel() ? $model->routeModel->title : $model->route,
                                ],
                                'latitude', 'longitude',
                                'city_id' => [
                                    'attribute' => 'city_id',
                                    'value' => function ($data) {
                                        $cities = \app\modules\admin\models\City::getCitiesList(true);
                                        return key_exists($data->city_id, $cities) ? $cities[$data->city_id] : false;
                                    },
                                    'filter' => \app\modules\admin\models\City::getCitiesList(true),
                                ]
                            ],
                        ]) ?>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
</div>
