<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\BotTrip */
/* @var $form yii\widgets\ActiveForm */

$luggages = [];
if (!$model->isNewRecord) {
    $luggages = $model->luggages;
    if (!empty($luggages)) {
        $luggages = \yii\helpers\ArrayHelper::getColumn($luggages, 'luggage_type');
    }
}
$luggages_v = implode(",", $luggages);
$all_luggages = \app\models\LuggageType::getAll();

$script = <<< JS
    function loadStartpoints(id) {
        $.ajax('/admin/bot-trip/start-points?id='+id, {
            type: "POST",
            async: false,
            data: {route: id},
            beforeSend: function (xhr) { },
            error: function (xhr) { console.log(xhr); },
            success: function (response) {
                console.log(response);
                $('#bottrip-startpoint_id').html('');
                $.each(response, function(key, value) {
                    var newOption = new Option(value, key, false, false);
                    $('#bottrip-startpoint_id').append(newOption).trigger('change');
                });
                $('#bottrip-startpoint_id').select2({
                    data: response
                });
            }
        });
    }
    function loadEndpoints(id) {
        $.ajax('/admin/bot-trip/end-points?id='+id, {
            type: "POST",
            async: false,
            data: {route: id},
            beforeSend: function (xhr) { },
            error: function (xhr) { console.log(xhr); },
            success: function (response) {
                console.log(response);
                $('#bottrip-endpoint_id').html('');
                $.each(response, function(key, value) {
                    var newOption = new Option(value, key, false, false);
                    $('#bottrip-endpoint_id').append(newOption).trigger('change');
                });
                $('#bottrip-endpoint_id').select2({
                    data: response
                });
            }
        });
    }
    $('#bottrip-route_id').change(function() {
        loadStartpoints($(this).val());
        loadEndpoints($(this).val());
    });
JS;
$this->registerJs($script, \yii\web\View::POS_READY);

?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?= $this->title; ?>
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="padding: 10px 0">
        <ul class="nav nav-tabs">
            <li class="active" style="margin-left: 15px;">
                <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Информация', [], false); ?></a>
            </li>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">

                    <div class="col-sm-6">

                        <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
                            'data' => \yii\helpers\ArrayHelper::map
                            (\app\modules\admin\models\User::find()
                                ->select(['id', 'name' => 'CONCAT(phone, \' \', first_name, \' \', second_name)'])
                                ->where(['type' => \app\modules\admin\models\User::TYPE_PASSENGER])->asArray()->all(),
                                'id', 'name'),
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'user_id',
                            'hideSearch' => true,
                            'options' => [
                                'placeholder' => Yii::t('app', "Пассажир")
                            ],
                            'pluginOptions' => ['allowClear' => true]
                        ]); ?>

                        <?= $form->field($model, 'route_id')->widget(Select2::classname(), [
                            'data' => \yii\helpers\ArrayHelper::map
                            (\app\modules\admin\models\Route::find()
                                ->where(['status' => \app\models\Route::STATUS_ACTIVE])->all(), 'id', 'title'),
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'route_id',
                            'hideSearch' => true,
                            'options' => [
                                'placeholder' => Yii::t('app', "Маршрут")
                            ],
                            'pluginOptions' => ['allowClear' => true]
                        ]); ?>

                        <?= $form->field($model, 'startpoint_id')->widget(Select2::classname(), [
                            'data' => \yii\helpers\ArrayHelper::map
                            (\app\modules\admin\models\Checkpoint::find()
                                ->where([
                                    'status' => \app\models\Checkpoint::STATUS_ACTIVE,
                                    'type' => [\app\models\Checkpoint::TYPE_START, \app\models\Checkpoint::TYPE_STOP]
                                ])->all(), 'id', 'title'),
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'startpoint_id',
                            'hideSearch' => true,
                            'options' => [
                                'placeholder' => Yii::t('app', "Остановка")
                            ],
                            'pluginOptions' => ['allowClear' => true]
                        ]); ?>

                        <?= $form->field($model, 'endpoint_id')->widget(Select2::classname(), [
                            'data' => \yii\helpers\ArrayHelper::map
                            (\app\modules\admin\models\Checkpoint::find()
                                ->where([
                                    'status' => \app\models\Checkpoint::STATUS_ACTIVE,
                                    'type' => [\app\models\Checkpoint::TYPE_STOP, \app\models\Checkpoint::TYPE_END]
                                ])->all(), 'id', 'title'),
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'endpoint_id',
                            'hideSearch' => true,
                            'options' => [
                                'placeholder' => Yii::t('app', "Конечная")
                            ],
                            'pluginOptions' => ['allowClear' => true]
                        ]); ?>

                        <?= $form->field($model, 'vehicle_type_id')->widget(Select2::classname(), [
                            'data' => \yii\helpers\ArrayHelper::map
                            (\app\modules\admin\models\VehicleType::find()
                                ->where(['status' => \app\models\Checkpoint::STATUS_ACTIVE,])->all(), 'id', 'title'),
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'vehicle_type_id',
                            'hideSearch' => true,
                            'options' => [
                                'placeholder' => Yii::t('app', "Тип авто")
                            ],
                            'pluginOptions' => ['allowClear' => true]
                        ]); ?>

                    </div>

                    <div class="col-sm-6">

                        <?= $form->field($model, 'status')->dropDownList($model::getStatusList()) ?>

                        <?= $form->field($model, 'seats')->textInput(['type' => 'number']) ?>

                        <?= $form->field($model, 'passenger_description')->textarea(['maxlength' => true, 'rows' => 3]) ?>

                        <?= $form->field($model, 'luggage', ['template' => '{label}<br>{input}{error}{hint}'])->dropdownList(
                            $all_luggages,
                            [
                                'placeholder' => Yii::$app->mv->gt('Типы кухни', [], false),
                                'selvalue' => $luggages_v,
                                'vn' => count($luggages),
                                'class' => 'sel2 mpsel outst',
                                'multiple' => 'true'
                            ]
                        )->label(Yii::$app->mv->gt('Багаж', [], false)); ?>
                    </div>

                </div>
            </div>


        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix text-right">
        <?= Html::submitButton(
            ($model->isNewRecord ?
                Yii::$app->mv->gt('{i} Добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-save'])], 0) :
                Yii::$app->mv->gt('{i} Сохранить', ['i' => Html::tag('i', '', ['class' => 'fa fa-save'])], 0)),
            ['class' => 'btn btn-success']
        ) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
