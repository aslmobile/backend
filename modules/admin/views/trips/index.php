<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\TripSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Trips',[],false);
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
                        Yii::$app->mv->gt('{i} new',['i'=>Html::tag('i','',['class'=>'fa fa-plus'])],false),
                        ['create'],
                        ['class' => 'btn btn-default btn-sm']
                    ); ?>
                    <?= Html::a(
                        Yii::$app->mv->gt('{i} delete selected',['i'=>Html::tag('i','',['class'=>'fa fa-fire'])],false),
                        [''],
                        [
                            'class' => 'btn btn-danger btn-sm',
                            'onclick'=>"
								var keys = $('#grid').yiiGridView('getSelectedRows');
								if (keys!='') {
									if (confirm('".Yii::$app->mv->gt('Are you sure you want to delete the selected items?',[],false)."')) {
										$.ajax({
											type : 'POST',
											data: {keys : keys},
											success : function(data) {}
										});
									}
								}
								return false;
							",
                        ]
                    ); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'grid',
                'layout'=>"
                    <div class='box-body' style='display: block;'><div class='col-sm-12 right-text'>{summary}</div><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>
                ",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\CheckboxColumn'],
                    'id',
                    'user_id' => [
                        'attribute' => 'user_id',
                        'content' => function ($data) {
                            return $data->user->fullName . '<br /><a href="tel:+' . $data->user->phone . '">+' . $data->user->phone . '</a>';
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
                                'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/user/select-users']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
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
                    'position' => [
                        'attribute' => 'position',
                        'content' => function ($data) {
                            return '<span class="badge bg-aqua-gradient link" role="button" data-toggle="modal" data-target="#map-location" data-map-location="' . $data->position . '">' . Yii::t('app', "Позиция на карте") . '</span>';
                        }
                    ],
                    'created_at' => [
                        'attribute' => 'created_at',
                        'value' => function ($module)
                        {
                            return Yii::$app->formatter->asDateTime($module->created_at);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<button type="button" class="btn btn-info btn-sm"><i class="fa fa-search"></i></button>', $url);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<button type="button" class="btn btn-success btn-sm"><i class="fa fa-pencil"></i></button>', $url);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    '<button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>',
                                    $url,
                                    ['data'=>[
                                        'confirm'=>Yii::$app->mv->gt('Are you sure you want to delete this item?',[],false),
                                        'method'=>'post',
                                        'pjax'=>'0'
                                    ]]
                                );
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </section>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
<?php $this->registerJs(new JsExpression("
    $(document).on('click', '.badge[data-map-location]', function () {
        var location = $(this).attr('data-map-location').split(\",\");
        
        var latitude = location[0],
            longitude = location[1];
        
        console.log(latitude, longitude);
    });
    
    function initMap() {
      var myLatLng = {lat: latitude, lng: longitude};
    
      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 4,
        center: myLatLng
      });
    
      var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        title: 'Hello World!'
      });
    }
"), yii\web\View::POS_READY); ?>
<div class="modal fade" id="map-location" tabindex="-1" role="dialog" aria-labelledby="Map Location">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', "Позиция на карте"); ?></h4>
            </div>
            <div class="modal-body">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>
