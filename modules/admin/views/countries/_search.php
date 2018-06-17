<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="countries-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title_ru') ?>

    <?= $form->field($model, 'title_ua') ?>

    <?= $form->field($model, 'title_be') ?>

    <?= $form->field($model, 'title_en') ?>

    <?php // echo $form->field($model, 'title_es') ?>

    <?php // echo $form->field($model, 'title_pt') ?>

    <?php // echo $form->field($model, 'title_de') ?>

    <?php // echo $form->field($model, 'title_fr') ?>

    <?php // echo $form->field($model, 'title_it') ?>

    <?php // echo $form->field($model, 'title_po') ?>

    <?php // echo $form->field($model, 'title_ja') ?>

    <?php // echo $form->field($model, 'title_lt') ?>

    <?php // echo $form->field($model, 'title_lv') ?>

    <?php // echo $form->field($model, 'title_cz') ?>

    <?php // echo $form->field($model, 'title_zh') ?>

    <?php // echo $form->field($model, 'title_he') ?>

    <?php // echo $form->field($model, 'code_alpha2') ?>

    <?php // echo $form->field($model, 'code_alpha3') ?>

    <?php // echo $form->field($model, 'code_iso') ?>

    <?php // echo $form->field($model, 'flag') ?>

    <?php // echo $form->field($model, 'dc') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
