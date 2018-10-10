<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Trip */
/* @var $form yii\widgets\ActiveForm */
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
                        <?= $form->field($model, 'status')->textInput() ?>

                        <?= $form->field($model, 'user_id')->textInput() ?>

                        <?= $form->field($model, 'amount')->textInput() ?>

                        <?= $form->field($model, 'tariff')->textInput() ?>

                        <?= $form->field($model, 'cancel_reason')->textInput() ?>

                        <?= $form->field($model, 'passenger_description')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'currency')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'payment_type')->textInput() ?>

                        <?= $form->field($model, 'passenger_rating')->textInput() ?>

                        <?= $form->field($model, 'startpoint_id')->textInput() ?>

                        <?= $form->field($model, 'route_id')->textInput() ?>

                        <?= $form->field($model, 'seats')->textInput() ?>

                        <?= $form->field($model, 'driver_comment')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'endpoint_id')->textInput() ?>

                        <?= $form->field($model, 'payment_status')->textInput() ?>

                        <?= $form->field($model, 'vehicle_type_id')->textInput() ?>

                        <?= $form->field($model, 'luggage_unique_id')->textInput() ?>

                        <?= $form->field($model, 'line_id')->textInput() ?>

                        <?= $form->field($model, 'passenger_comment')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'driver_rating')->textInput() ?>

                        <?= $form->field($model, 'vehicle_id')->textInput() ?>

                        <?= $form->field($model, 'driver_id')->textInput() ?>

                        <?= $form->field($model, 'need_taxi')->textInput() ?>

                        <?= $form->field($model, 'taxi_status')->textInput() ?>

                        <?= $form->field($model, 'taxi_cancel_reason')->textInput() ?>

                        <?= $form->field($model, 'taxi_address')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'taxi_time')->textInput() ?>

                        <?= $form->field($model, 'start_time')->textInput() ?>

                        <?= $form->field($model, 'finish_time')->textInput() ?>

                        <?= $form->field($model, 'driver_description')->textInput(['maxlength' => true]) ?>

                    </div>
                    <div class="col-sm-6">

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
