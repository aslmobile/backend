<?php

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\VehicleType | app\modules\admin\models\VehicleModel | app\modules\admin\models\VehicleBrand */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'image')->widget(alexantr\elfinder\InputFile::className(), [
                    'buttonText' => Yii::$app->mv->gt('Выбрать', [], false),
                    'options' => [
                        'language' => 'ru',
                        'class' => 'form-control',
                        'onchange' => <<<JS
                        var changed = $(this);
                        var val = $(changed).val();
                        setTimeout(function() {
                        if($(".elfinder-input-preview").length){
                           $(".elfinder-input-preview").html($("<img/>", {src : val, width: 200, height: 200}));
                        }else{
                            $(changed).parent().after('<div class="help-block elfinder-input-preview"></div');
                            $(".elfinder-input-preview").html($("<img/>", {src : val, width: 200, height: 200}));
                        }
                        }, 500);
JS
                    ],
                    'clientRoute' => 'el-finder/input',
                    'filter' => ['image'],
                    'preview' => function ($value) {
                        return yii\helpers\Html::img($value, ['width' => 200, 'id' => 'elfinder_preview']);
                    },
                ]); ?>
                <?= $form->field($model, 'max_seats')->textInput(['type' => 'number', 'step' => 1, 'min' => 0]) ?>
                <?= $form->field($model, 'status')->dropDownList($model->statusList) ?>
            </div>
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
