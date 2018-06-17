<?php

use alexantr\elfinder\InputFile;
use app\modules\admin\models\Lang;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use alexantr\tinymce\TinyMCE as TTinyMCE;
use alexantr\elfinder\TinyMCE as ETinyMCE;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Tune */
/* @var $form yii\widgets\ActiveForm */

?>


<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"></h3>
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
                            <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

                            <? if(!$model->isNewRecord) { ?>
                                <?
                                $types = [
                                    'textInput',
                                    'image',
                                    'file',
                                    'textarea',
                                    'textareaMce',
                                    'date'
                                ];
                                switch ($types[$model->widget]) {
                                    case 'textInput':
                                        ?>
                                        <?= $form->field($model, 'val')->textInput(['maxlength' => true]) ?>
                                        <?
                                        break;
                                    case 'image':
                                        ?>
                                        <?= Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $model->val, 'x100') ?>
                                        <?= $form->field($model, 'val')->widget(InputFile::className(), [
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
                                        <?
                                        break;
                                    case 'file':
                                        ?>
                                        <?= $form->field($model, 'val')->widget(InputFile::className(), [
                                        'buttonText' => Yii::$app->mv->gt('Select', [], false),
                                        'options' => [
                                            'language' => 'en',
                                            'class' => 'form-control',
                                        ],
                                        'clientRoute' => 'el-finder/input',
                                    ]); ?>
                                        <?
                                        break;
                                    case 'textarea':
                                        ?>
                                        <?= $form->field($model, 'val')->textarea(['rows' => 6]) ?>
                                        <?
                                        break;
                                    case 'textareaMce':
                                        ?>
                                        <?= $form->field($model, 'val')->widget(TTinyMCE::className(), [
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
                                        <?
                                        break;
                                    case 'date':
                                        ?>
                                        <?= $form->field($model, 'val')->widget(\yii\jui\DatePicker::classname(), [
                                        'language' => 'ru',
                                        'options' => [
                                            'class' => 'form-control'
                                        ],
                                        'clientOptions' => [
                                            'changeMonth' => true,
                                            'changeYear' => true,
                                            'showButtonPanel' => true,
                                        ],
                                        'dateFormat' => 'dd.MM.yyyy',
                                    ]) ?>
                                        <?
                                        break;
                                }
                                ?>
                            <? } ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'widget')->dropdownList([
                                'textInput',
                                'image',
                                'file',
                                'textarea',
                                'textareaMce',
                                'date'
                            ], ['name' => 'Tune[widget]']); ?>

                        </div>
                    </div>
                </div>
                <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                    <div class="tab-pane fade" id="top-<?= $k ?>">
                        <div class="row">
                            <div class="col-sm-12">
                                <? if(!$model->isNewRecord) { ?>
                                    <?
                                    $types = [
                                        'textInput',
                                        'image',
                                        'file',
                                        'textarea',
                                        'textareaMce',
                                        'date'
                                    ];
                                    switch ($types[$model->widget]) {
                                        case 'textInput':
                                            ?>
                                            <?= $form->field($model, 'val_' . $k)->textInput(['maxlength' => true]) ?>
                                            <?
                                            break;
                                        case 'image':
                                            $mval = 'val_' . $k;
                                            ?>
                                            <?= Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $mval, 'x100') ?>
                                            <?= $form->field($model, $mval)->widget(InputFile::className(), [
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
                                            <?
                                            break;
                                        case 'file':
                                            ?>
                                            <?= $form->field($model, 'val_' . $k)->widget(InputFile::className(), [
                                            'buttonText' => Yii::$app->mv->gt('Select', [], false),
                                            'options' => [
                                                'language' => 'en',
                                                'class' => 'form-control',
                                            ],
                                            'clientRoute' => 'el-finder/input',
                                        ]); ?>
                                            <?
                                            break;
                                        case 'textarea':
                                            ?>
                                            <?= $form->field($model, 'val_' . $k)->textarea(['rows' => 6]) ?>
                                            <?
                                            break;
                                        case 'textareaMce':
                                            ?>
                                            <?= $form->field($model, 'val_' . $k)->widget(TTinyMCE::className(), [
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
                                            <?
                                            break;
                                        case 'date':
                                            ?>
                                            <?= $form->field($model, 'val_' . $k)->widget(\yii\jui\DatePicker::classname(), [
                                            'language' => 'ru',
                                            'options' => [
                                                'class' => 'form-control'
                                            ],
                                            'clientOptions' => [
                                                'changeMonth' => true,
                                                'changeYear' => true,
                                                'showButtonPanel' => true,
                                            ],
                                            'dateFormat' => 'dd.MM.yyyy',
                                        ]) ?>
                                            <?
                                            break;
                                    }
                                    ?>
                                <? } ?>
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