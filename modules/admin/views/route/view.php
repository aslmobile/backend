<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\Route */

$this->title = Yii::$app->mv->gt($model->title,[],false);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Routes'), 'url' => ['index']];
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
            <div class="col-sm-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3></h3>
                        <div class="box-tools pull-right">
                            <?= Html::a(Yii::t('app', 'Изменить'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                            <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                            ],
                            ]) ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <?= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    'title',
                                    'status' => [
                                        'attribute' => 'status',
                                        'value' => function ($data) {
                                            return key_exists($data->status, $data->statusList) ? $data->statusList[$data->status] : false;
                                        }
                                    ],
                                    'start_city_id' => [
                                        'attribute' => 'start_city_id',
                                        'value' => function ($data) {
                                            $cities = \app\modules\admin\models\City::getCitiesList(true);
                                            return key_exists($data->start_city_id, $cities) ? $cities[$data->start_city_id] : false;
                                        }
                                    ],
                                    'end_city_id' => [
                                        'attribute' => 'end_city_id',
                                        'value' => function ($data) {
                                            $cities = \app\modules\admin\models\City::getCitiesList(true);
                                            return key_exists($data->end_city_id, $cities) ? $cities[$data->end_city_id] : false;
                                        }
                                    ],
                                    'base_tariff'
                                ],
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
