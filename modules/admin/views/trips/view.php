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
                <div class="box box-widget">
                    <div class="box-header bg-aqua with-border">
                        <i class="fa fa-info"></i> <?= Yii::t('app', "Ифномация о заказе"); ?>
                        <div class="box-tools">
                            <span class="btn btn-sm btn-success" role="button" data-toggle="modal" data-target="#map-location" data-map-location="<?= $model->position; ?>">
                                <?= Yii::t('app', "Показать на карте"); ?>
                            </span>
                            |
                            <?= Html::a('Редактировать', [
                                'update',
                                'id' => $model->id
                            ], ['class' => 'btn btn-sm btn-primary']) ?>
                        </div>
                    </div>
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-sm-4 col-md-3 border-right">
                                <div class="description-block">
                                    <h5 class="description-header"><?= $model->summarySeats; ?></h5>
                                    <span class="description-text"><?= Yii::t('app', "Места"); ?></span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 col-md-6 border-right">
                                <div class="description-block">
                                    <h5 class="description-header"><?= $model->summaryAmount; ?>₸</h5>
                                    <span class="description-text"><?= Yii::t('app', "Стоимость"); ?></span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 col-md-3">
                                <div class="description-block">
                                    <h5 class="description-header"><?= Yii::$app->formatter->asTime($model->start_time); ?></h5>
                                    <span class="description-text"><?= Yii::t('app', "Время"); ?></span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                    <div class="box-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'attribute' => 'user_id',
                                    'value' => function ($model)
                                    {
                                        return $model->user->fullName;
                                    }
                                ],
                                [
                                    'attribute' => 'user_id',
                                    'label' => Yii::t('app', "Телефон"),
                                    'value' => function ($model)
                                    {
                                        return '<a href="tel:+' . $model->user->phone . '">+' . $model->user->phone . '</a>';
                                    }, 'format' => 'html'
                                ],
                                'seats' => [
                                    'attribute' => 'seats',
                                    'value' => function ($model)
                                    {
                                        $luggages = \app\models\TripLuggage::find()->where(['unique_id' => $model->luggage_unique_id, 'need_place' => 1])->count();
                                        if ($luggages > 0) $luggages = ' (+' . $luggages . ')';
                                        return $model->seats . $luggages;
                                    }
                                ],
                                [
                                    'attribute' => 'startpoint_id',
                                    'value' => function ($model)
                                    {
                                        return $model->startpoint->title;
                                    }
                                ],
                                [
                                    'attribute' => 'endpoint_id',
                                    'value' => function ($model)
                                    {
                                        return $model->endpoint->title;
                                    }
                                ],
                                [
                                    'attribute' => 'status',
                                    'value' => function ($data) {
                                        return key_exists($data->status, $data->statusList) ? $data->statusList[$data->status] : false;
                                    }
                                ],
                                [
                                    'attribute' => 'route_id',
                                    'value' => function ($data) {
                                        return $data->route ? $data->route->title : Yii::t('app', "Не задано");
                                    }
                                ],
                                [
                                    'attribute' => 'vehicle_type_id',
                                    'value' => function ($data) {
                                        return $data->vehicleType ? $data->vehicleType->title : Yii::t('app', "Не задано");
                                    }
                                ],
                                [
                                    'attribute' => 'calculatedAmount',
                                    'label' => Yii::t('app', "Стоимость")
                                ]
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="box box-widget">
                    <div class="box-header bg-yellow with-border"><i class="fa fa-taxi"></i> <?= Yii::t('app', "Информация о такси"); ?></div>
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

            <?php $luggages = \app\models\TripLuggage::find()->where(['unique_id' => $model->luggage_unique_id])->all(); ?>
            <?php if ($luggages && count($luggages) > 0) : ?>
            <div class="col-sm-12">
                <div class="box box-widget">
                    <div class="box-header with-border">
                        <i class="fa fa-shopping-bag"></i> <?= Yii::t('app', "Информация о багаже"); ?>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <?php
                            foreach ($luggages as $luggage) : ?>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="box-header bg-info"><?= Yii::t('app', "Багаж"); ?> №<?= $luggage->id; ?></div>
                                    <?= DetailView::widget([
                                        'model' => $luggage,
                                        'attributes' => [
                                            'seats',
                                            'need_place',
                                            'amount',
                                            'seats'
                                        ],
                                    ]) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>


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
