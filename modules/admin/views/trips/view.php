<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\Trip */

$this->title = Yii::$app->mv->gt($model->id,[],false);
$this->params['breadcrumbs'][] = ['label' => 'Trips', 'url' => ['index']];
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
                            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                            <?= Html::a('Remove', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
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
                                            'id',
            'created_at',
            'updated_at',
            'status',
            'user_id',
            'amount',
            'tariff',
            'cancel_reason',
            'passenger_description',
            'created_by',
            'updated_by',
            'currency',
            'payment_type',
            'passenger_rating',
            'startpoint_id',
            'route_id',
            'seats',
            'driver_comment',
            'endpoint_id',
            'payment_status',
            'vehicle_type_id',
            'luggage_unique_id',
            'line_id',
            'passenger_comment',
            'driver_rating',
            'vehicle_id',
            'driver_id',
            'need_taxi',
            'taxi_status',
            'taxi_cancel_reason',
            'taxi_address',
            'taxi_time:datetime',
            'scheduled',
            'schedule_id',
            'start_time:datetime',
            'finish_time:datetime',
            'driver_description',
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
