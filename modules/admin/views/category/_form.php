<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use alexantr\elfinder\InputFile;
use alexantr\tinymce\TinyMCE as TTinyMCE;
use alexantr\elfinder\TinyMCE as ETinyMCE;
use app\modules\admin\models\Lang;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Category */
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
                <a data-toggle="tab" href="#top">Data</a>
            </li>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status')->dropDownList($model->statuses) ?>

                        <?= $form->field($model, 'sort')->textInput() ?>

                        <?= $form->field($model, 'image')->widget(InputFile::className(), [
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
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'file')->widget(InputFile::className(), [
                            'buttonText' => Yii::$app->mv->gt('Select', [], false),
                            'options' => [
                                'language' => 'en',
                                'class' => 'form-control',
                            ],
                            'clientRoute' => 'el-finder/input',
                            'filter' => ['application/pdf'],
                        ]); ?>

                        <?= $form->field($model, 'small_image')->widget(InputFile::className(), [
                            'buttonText' => Yii::$app->mv->gt('Выбрать', [], false),
                            'options' => [
                                'language' => 'ru',
                                'class' => 'form-control',
                                'onchange' => <<<JS
                                    var changed = $(this);
                                    var val = $(changed).val();
                                    setTimeout(function() {
                                    if($(".elfinder-input-preview-2").length){
                                       $(".elfinder-input-preview-2").html($("<img/>", {src : val, width: 200, height: 200}));
                                    }else{
                                        $(changed).parent().after('<div class="help-block elfinder-input-preview-2"></div');
                                        $(".elfinder-input-preview-2").html($("<img/>", {src : val, width: 200, height: 200}));
                                    }
                                    }, 500);
JS
                            ],
                            'clientRoute' => 'el-finder/input',
                            'filter' => ['image'],
                            'preview' => function ($value) {
                                return yii\helpers\Html::img($value, ['width' => 200, 'id' => 'elfinder_preview-2']);
                            },
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'short_description')->widget(TTinyMCE::className(), [
                            'clientOptions' => [
                                'language_url' => Yii::$app->homeUrl . 'tiny_translates/ru.js',
                                'convert_urls' => false,
                                'language' => 'en',
                                'plugins' => [
                                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                    "searchreplace visualblocks visualchars code fullscreen",
                                    "insertdatetime media nonbreaking save table contextmenu directionality",
                                    "template paste textcolor emoticons",
                                ],
                                'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview | forecolor backcolor | ",
                                'file_picker_callback' => ETinyMCE::getFilePickerCallback(['el-finder/tinymce']),
                            ],
                        ]) ?>

                        <?= $form->field($model, 'description')->widget(TTinyMCE::className(), [
                            'clientOptions' => [
                                'language_url' => Yii::$app->homeUrl . 'tiny_translates/ru.js',
                                'convert_urls' => false,
                                'language' => 'en',
                                'plugins' => [
                                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                    "searchreplace visualblocks visualchars code fullscreen",
                                    "insertdatetime media nonbreaking save table contextmenu directionality",
                                    "template paste textcolor emoticons",
                                ],
                                'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview | forecolor backcolor | ",
                                'file_picker_callback' => ETinyMCE::getFilePickerCallback(['el-finder/tinymce']),
                            ],
                        ]) ?>
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
