<?php
use app\modules\admin\models\Lang;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>


<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>

<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::$app->mv->gt("Основное", [], 0) ?></h3>
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
                        <?= $form->field($model, 'logo')->widget(alexantr\elfinder\InputFile::className(), [
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
                        <?= $form->field($model, 'logo_small')->widget(alexantr\elfinder\InputFile::className(), [
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
                        <?= $form->field($model, 'name', ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'title', ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'description', ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'keywords', ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'copy', ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'descr')->widget(alexantr\tinymce\TinyMCE::className(), [
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
                        ]); ?>
                    </div>
                </div>
            </div>
            <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                <div class="tab-pane fade" id="top-<?= $k ?>">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'name_' . $k, ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true])->label($model->getAttributeLabel('name') . ' ' . $v) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'title_' . $k, ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true])->label($model->getAttributeLabel('title') . ' ' . $v) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'description_' . $k, ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true])->label($model->getAttributeLabel('description') . ' ' . $v) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keywords_' . $k, ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true])->label($model->getAttributeLabel('keywords') . ' ' . $v) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'copy_' . $k, ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true])->label($model->getAttributeLabel('copy') . ' ' . $v) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= $form->field($model, 'descr_' . $k)->widget(alexantr\tinymce\TinyMCE::className(), [
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
                            ])->label($model->getAttributeLabel('descr') . ' ' . $v) ?>
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
<div class="box">
    <div class="box-header with-border">
        <?= Yii::$app->mv->gt("Контактные данные", [], 0) ?>
        <div class="box-tools pull-right">
            <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="padding: 10px 0">
        <ul class="nav nav-tabs">
            <li class="active" style="margin-left: 15px;">
                <a data-toggle="tab" href="#middle"><?= Yii::$app->mv->gt('Данные', [], false) ?></a>
            </li>
            <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                <li>
                    <a data-toggle="tab" href="#middle-<?= $k ?>" style="max-height: 42px;"><?= $v ?></a>
                </li>
            <?php } ?>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="middle" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'phone', ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'addphone', ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'site_email', ['template' => '{label}{input}{error}{hint}'])->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'address', ['template' => '{label}{input}{error}{hint}'])->textarea(['rows' => '4']) ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'social', ['template' => '{label}{input}{error}{hint}'])->textarea(['rows' => '4']) ?>
                    </div>
                </div>
            </div>
            <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                <div class="tab-pane fade" id="middle-<?= $k ?>">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'address_' . $k, ['template' => '{label}{input}{error}{hint}'])->textarea(['rows' => '4'])->label($model->getAttributeLabel('address') . ' ' . $v) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= $form->field($model, 'social_' . $k, ['template' => '{label}{input}{error}{hint}'])->textarea(['rows' => '4'])->label($model->getAttributeLabel('social') . ' ' . $v) ?>
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
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Yii::$app->mv->gt("Системные настройки", [], 0) ?></h3>
        <div class="box-tools pull-right">
            <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="padding: 10px 0">
        <ul class="nav nav-tabs">
            <li class="active" style="margin-left: 15px;">
                <a data-toggle="tab" href="#system-settings"><?= Yii::$app->mv->gt('Данные', [], false) ?></a>
            </li>
            <li>
                <a data-toggle="tab" href="#driver-settings"><?= Yii::$app->mv->gt('Настройки водителя', [], false) ?></a>
            </li>
            <li>
                <a data-toggle="tab" href="#passenger-settings"><?= Yii::$app->mv->gt('Настройки пассажира', [], false) ?></a>
            </li>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="system-settings" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'maint')->dropdownList([
                            0 => Yii::$app->mv->gt("Отключен", [], 0),
                            1 => Yii::$app->mv->gt("Включен", [], 0)
                        ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'syscache')->dropdownList([
                            0 => Yii::$app->mv->gt("Отключен", [], 0),
                            1 => Yii::$app->mv->gt("Включен", [], 0)
                        ]); ?>
                    </div>
                </div>
            </div>
            <div id="driver-settings" class="tab-pane fade">
                <div class="row">
                    <div class="col-sm-12 col-lg-2"><?= $form->field($model, 'driver_commission')->textInput(['type' => 'number']); ?></div>
                </div>
            </div>
            <div id="passenger-settings" class="tab-pane fade">
                <div class="row">
                    <div class="col-sm-12 col-lg-2"><?= $form->field($model, 'passenger_fine')->textInput(['type' => 'number']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix text-right">
        <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<!-- /.nav-tabs-custom -->


