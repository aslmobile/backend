<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\TripSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trip-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'tariff') ?>

    <?php // echo $form->field($model, 'cancel_reason') ?>

    <?php // echo $form->field($model, 'passenger_description') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'currency') ?>

    <?php // echo $form->field($model, 'payment_type') ?>

    <?php // echo $form->field($model, 'passenger_rating') ?>

    <?php // echo $form->field($model, 'startpoint_id') ?>

    <?php // echo $form->field($model, 'route_id') ?>

    <?php // echo $form->field($model, 'seats') ?>

    <?php // echo $form->field($model, 'driver_comment') ?>

    <?php // echo $form->field($model, 'endpoint_id') ?>

    <?php // echo $form->field($model, 'payment_status') ?>

    <?php // echo $form->field($model, 'vehicle_type_id') ?>

    <?php // echo $form->field($model, 'luggage_unique_id') ?>

    <?php // echo $form->field($model, 'line_id') ?>

    <?php // echo $form->field($model, 'passenger_comment') ?>

    <?php // echo $form->field($model, 'driver_rating') ?>

    <?php // echo $form->field($model, 'vehicle_id') ?>

    <?php // echo $form->field($model, 'driver_id') ?>

    <?php // echo $form->field($model, 'need_taxi') ?>

    <?php // echo $form->field($model, 'taxi_status') ?>

    <?php // echo $form->field($model, 'taxi_cancel_reason') ?>

    <?php // echo $form->field($model, 'taxi_address') ?>

    <?php // echo $form->field($model, 'taxi_time') ?>

    <?php // echo $form->field($model, 'scheduled') ?>

    <?php // echo $form->field($model, 'schedule_id') ?>

    <?php // echo $form->field($model, 'start_time') ?>

    <?php // echo $form->field($model, 'finish_time') ?>

    <?php // echo $form->field($model, 'driver_description') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
