<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Trip */

$this->title = Yii::$app->mv->gt($model->id,[],false);
$this->params['breadcrumbs'][] = ['label' => 'Trips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>&nbsp;</h1>
        <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-8">
                <div class="box">
                    <div class="box-header with-border">
                        Информация о заказе
                        <div class="box-tools">
                            <span class="btn btn-sm btn-primary" role="button" data-toggle="modal" data-target="#map-location" data-map-location="<?= $model->position; ?>">
                                <?= Yii::t('app', "Показать на карте"); ?>
                            </span>
                        </div>
                    </div>
                    <div class="box-body">

                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="box box-widget">
                    <div class="box-header bg-orange with-border"><?= Yii::t('app', "Такси"); ?></div>
                    <div class="box-body">
                        <?php if ($model->need_taxi) : ?>
                            <?= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    'taxi_status' => [
                                        'attribute' => 'taxi_status',
                                        'value' => function ($model) {
                                            return key_exists($model->taxi_status, $model->taxiStatusList) ? $model->taxiStatusList[$model->taxi_status] : false;
                                        }
                                    ],
                                    'taxi_cancel_reason',
                                    'taxi_address',
                                    'taxi_time:datetime'
                                ],
                            ]) ?>
                        <?php else : ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="description-block">
                                        <span class="description-text"><?= Yii::t('app', "Такси не нужно"); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3></h3>
                        <div class="box-tools pull-right">
                            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                            <?= Html::a('Добавить', ['delete', 'id' => $model->id], [
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
            <?php $this->registerJsFile("https://maps.googleapis.com/maps/api/js?key=AIzaSyALfPPffcWHUHCDKccaIlBj5kLfQjIcD9w&callback=initMap"); ?>
            <?php $this->registerJs(new JsExpression("    
    function initMap() {
      
    }
"), yii\web\View::POS_BEGIN); ?>
            <?php $this->registerJs(new JsExpression("
    $(document).on('click', '[data-map-location]', function () {
        var location = $(this).attr('data-map-location').split(\",\");
        
        var latitude = location[0],
            longitude = location[1];
            
        var myLatLng = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
        
        console.log(myLatLng);
            
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: myLatLng,
            mapTypeId: 'roadmap'
        });
        
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            icon: '/img/passenger_marker.png'
        });
    });
"), yii\web\View::POS_READY); ?>
            <div class="modal fade" id="map-location" tabindex="-1" role="dialog" aria-labelledby="Map Location">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title"><?= Yii::t('app', "Позиция на карте"); ?></h4>
                        </div>
                        <div class="modal-body">
                            <div id="map" style="width: 100%; min-height: 450px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
