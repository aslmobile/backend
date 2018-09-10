<?php

use app\components\widgets\Alert;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */

$this->title = Yii::$app->mv->gt('Пассажир', [], false);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Боты', [], false), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <section class="content-header">
        <h2><?= Html::encode($this->title) ?></h2>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <?= Alert::widget() ?>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua">
                        <h3 class="box-title"><?= Yii::t('app', "Создание очереди"); ?></h3>
                    </div>
                    <div class="box-body">
                        <p>Бот создаст очереди из пассажиров согласно моку данных</p>
                        <?= $form->field($model, 'action_type')->hiddenInput(['value' => 1])->label(false); ?>
                    </div>
                    <div class="box-footer text-center">
                        <?php echo \yii\helpers\Html::submitButton(
                            Yii::$app->mv->gt('Создать', [], 0),
                            ['class' => 'btn btn-success']
                        ); ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>

            <div class="col-sm-12 col-md-4">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua">
                        <h3 class="box-title"><?= Yii::t('app', "Посадить в машину"); ?></h3>
                    </div>
                    <div class="box-body">
                        <?= $form->field($model, 'action_type')->hiddenInput(['value' => 2])->label(false); ?>
                        <?= $form->field($model, 'driver_id')->widget(Select2::classname(), [
                            'model' => [],
                            'theme' => Select2::THEME_DEFAULT,
                            'attribute' => 'driver_id',
                            'hideSearch' => true,
                            'options' => [
                                'placeholder' => Yii::t('app', "Водитель")
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 1,
                                'ajax' => [
                                    'url' => \yii\helpers\Url::toRoute(['/admin/user/select-drivers']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression('function(user) { return user.text; }'),
                                'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                                'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("' . \yii\helpers\Url::toRoute(['/admin/user/select-drivers']) . '", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                            ],
                        ]); ?>
                    </div>
                    <div class="box-footer text-center">
                        <?php echo \yii\helpers\Html::submitButton(
                            Yii::$app->mv->gt('Посадить', [], 0),
                            ['class' => 'btn btn-success']
                        ); ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </section>
</div>
