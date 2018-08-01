<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use alexantr\elfinder\InputFile;
use alexantr\tinymce\TinyMCE as TTinyMCE;
use alexantr\elfinder\TinyMCE as ETinyMCE;
use app\modules\admin\models\Lang;
/* @var $this yii\web\View */
/* @var $model app\models\Route */
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
                <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Информация',[],false); ?></a>
            </li>
                    </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'status')->dropDownList($model::getStatusList()) ?>


                        <?= $form->field($model, 'start_city_id')->dropDownList(\app\modules\admin\models\City::getCitiesList(true)) ?>

                        <?= $form->field($model, 'end_city_id')->dropDownList(\app\modules\admin\models\City::getCitiesList(true)) ?>

                        <?= $form->field($model, 'base_tariff')->textInput() ?>
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
            Yii::$app->mv->gt('{i} Добавить',['i'=>Html::tag('i','',['class'=>'fa fa-save'])],0) :
            Yii::$app->mv->gt('{i} Сохранить',['i'=>Html::tag('i','',['class'=>'fa fa-save'])],0)),
            ['class' => 'btn btn-success']
        ) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
