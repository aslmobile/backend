<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="dynamic-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'category_id') ?>

    <?= $form->field($model, 'group_id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'image_count') ?>

    <?= $form->field($model, 'gallery_count') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'created_by') ?>

    <?= $form->field($model, 'updated_at') ?>

    <?= $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
