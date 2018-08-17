<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

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
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua">
                        <h3 class="box-title"><?= Yii::t('app', "Создание очереди"); ?></h3>
                    </div>
                    <div class="box-body">

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
                        <h3 class="box-title"><?= Yii::t('app', "Отменить поездку"); ?></h3>
                    </div>
                    <div class="box-body">

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
        </div>
    </section>
</div>
