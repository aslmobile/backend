<?php

use app\modules\admin\models\Lang;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$attributes = array_keys($model->getAttributes());
$lang_attributes = preg_grep('/title/', $attributes);
$current_language_code = Lang::$current->url;
$current_language_index = array_search('title_' . $current_language_code, $lang_attributes);
$current_lang_attribute = $lang_attributes[$current_language_index];
unset($lang_attributes[$current_language_index]);
$lang_labels = $lang_attributes;
foreach ($lang_labels as $key => $value) {
    $lang_labels[$key] = explode('_', $value)[1];
}

/* @var $this yii\web\View */
/* @var $model app\models\Countries */
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
                <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Данные', [], false) ?></a>
            </li>
            <?php foreach ($lang_labels as $k => $v) { ?>
                <li>
                    <a data-toggle="tab" href="#top-<?= $k ?>" style="max-height: 42px;"><?= ucfirst($v) ?></a>
                </li>
            <?php } ?>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, $current_lang_attribute)->textInput(['maxlength' => true])->label($model->getAttributeLabel('title') . ' ' . ucfirst($current_language_code)) ?>

                        <?= $form->field($model, 'code_alpha2')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'code_alpha3')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'code_iso')->textInput() ?>
                    </div>
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
                        ]); ?>
                    </div>
                </div>
            </div>
            <?php foreach ($lang_labels as $k => $v) { ?>
                <div class="tab-pane fade" id="top-<?= $k ?>">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'title_' . $v)->label($model->getAttributeLabel('title') . ' ' . ucfirst($v)); ?>
                        </div>
                        <div class="col-sm-6"></div>
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
