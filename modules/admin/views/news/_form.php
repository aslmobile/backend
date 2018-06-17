<?php

use app\modules\admin\models\Lang;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\News */
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
            <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="padding: 10px 0">
        <ul class="nav nav-tabs">
            <li class="active" style="margin-left: 15px;">
                <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Данные', [], false) ?></a>
            </li>
            <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                <li>
                    <a data-toggle="tab" href="#top-<?= $k ?>" style="max-height: 42px;"><?= $v ?></a>
                </li>
            <?php } ?>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'category_id')->dropDownList(ArrayHelper::map(\app\models\NewsCategories::find()->all(), 'id', 'title')) ?>
                    </div>
                    <div class="col-sm-6">
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

                        <?= $form->field($model, 'status')->dropDownList($model->statuses) ?>

                        <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'short_description')->widget(alexantr\tinymce\TinyMCE::className(), [
                            'clientOptions' => [
                                'language_url' => 'https://olli-suutari.github.io/tinyMCE-4-translations/ru.js',
                                'language' => 'ru',
                                'plugins' => [
                                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                    "searchreplace visualblocks visualchars code fullscreen",
                                    "insertdatetime media nonbreaking save table contextmenu directionality",
                                    "template paste textcolor emoticons",
                                ],
                                'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor | emoticons",
                                'file_picker_callback' => alexantr\elfinder\TinyMCE::getFilePickerCallback(['el-finder/tinymce']),
                            ],
                        ]) ?>

                        <?= $form->field($model, 'description')->widget(alexantr\tinymce\TinyMCE::className(), [
                            'clientOptions' => [
                                'language_url' => 'https://olli-suutari.github.io/tinyMCE-4-translations/ru.js',
                                'language' => 'ru',
                                'plugins' => [
                                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                    "searchreplace visualblocks visualchars code fullscreen",
                                    "insertdatetime media nonbreaking save table contextmenu directionality",
                                    "template paste textcolor emoticons",
                                ],
                                'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor | emoticons",
                                'file_picker_callback' => alexantr\elfinder\TinyMCE::getFilePickerCallback(['el-finder/tinymce']),
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>

            <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                <div class="tab-pane fade" id="top-<?= $k ?>">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'title_' . $k)->label($model->getAttributeLabel('title') . ' ' . $v); ?>
                        </div>
                        <div class="col-sm-6"></div>
                        <div class="col-sm-12">
                            <?= $form->field($model, 'short_description_' . $k)->widget(alexantr\tinymce\TinyMCE::className(), [
                                'clientOptions' => [
                                    'language_url' => 'https://olli-suutari.github.io/tinyMCE-4-translations/ru.js',
                                    'language' => 'ru',
                                    'plugins' => [
                                        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                        "searchreplace visualblocks visualchars code fullscreen",
                                        "insertdatetime media nonbreaking save table contextmenu directionality",
                                        "template paste textcolor emoticons",
                                    ],
                                    'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor | emoticons",
                                    'file_picker_callback' => alexantr\elfinder\TinyMCE::getFilePickerCallback(['el-finder/tinymce']),
                                ],
                            ])->label($model->getAttributeLabel('short_description') . ' ' . $v) ?>

                            <?= $form->field($model, 'description_' . $k)->widget(alexantr\tinymce\TinyMCE::className(), [
                                'clientOptions' => [
                                    'language_url' => 'https://olli-suutari.github.io/tinyMCE-4-translations/ru.js',
                                    'language' => 'ru',
                                    'plugins' => [
                                        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                        "searchreplace visualblocks visualchars code fullscreen",
                                        "insertdatetime media nonbreaking save table contextmenu directionality",
                                        "template paste textcolor emoticons",
                                    ],
                                    'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor | emoticons",
                                    'file_picker_callback' => alexantr\elfinder\TinyMCE::getFilePickerCallback(['el-finder/tinymce']),
                                ],
                            ])->label($model->getAttributeLabel('description') . ' ' . $v) ?>
                        </div>

                    </div>

                </div>

            <?php } ?>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix text-right">
        <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
