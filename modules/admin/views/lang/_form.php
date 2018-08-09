<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$yes_no = Yii::$app->params['yes_no'];
?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::$app->mv->gt('Языки', [], false) ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="padding: 10px 0">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 15px;">
                        <a data-toggle="tab" href="#verif"><?= Yii::$app->mv->gt('Информация', [], false) ?></a>
                    </li>
                </ul>
                <div class="tab-content" style="padding: 10px">
                    <div class="tab-pane active" id="verif">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'flag')->widget(alexantr\elfinder\InputFile::className(), [
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
                                ])->label(false) ?>
                                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'local')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
                                <?= $form->field($model, 'default')->dropdownList($yes_no) ?>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer clearfix text-right">
                        <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
