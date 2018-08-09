<?php

use app\modules\admin\models\AuthItem;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$yesno = Yii::$app->params['yesno'];

?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">
    <div class="col-sm-12 col-lg-8">
        <div class="box box-widget">
            <div class="box-header with-border bg-aqua">
                <h3 class="box-title"><?= Yii::$app->mv->gt('Информация', [], false) ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12"><?= $form->field($model, 'status')->dropDownList($model->statuses) ?></div>
                    <div class="col-sm-12"><?= $form->field($model, 'type')->dropDownList($model->types) ?></div>
                    <div class="col-sm-12 col-md-6"><?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-sm-12 col-md-6"><?= $form->field($model, 'second_name')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-sm-12 col-md-6"><?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-sm-12 col-md-6"><?= $form->field($model, 'phone')->textInput() ?></div>
                    <div class="col-sm-12"><?= $form->field($model, 'password')->passwordInput() ?></div>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
        <!-- /.box -->

        <div class="box box-widget">
            <div class="box-header with-border bg-aqua">
                <h3 class="box-title"><?= Yii::t('app', "Права доступа"); ?></h3>
            </div>
            <div class="box-body">
                <?= Html::checkboxList('roles',
                    array_keys(ArrayHelper::map(Yii::$app->authManager->getRolesByUser($model->id), 'name', 'description')),
                    ArrayHelper::map(AuthItem::find()->orderBy('description ASC')->all(), 'name', 'description'),
                    [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            $check = $checked ? ' checked="checked"' : '';
                            return "<div class=\"col-sm-12\"><label><input type=\"checkbox\" name=\"$name\" value=\"$value\"$check><span class='role_label'>$label</span></label></div>";
                        }
                    ]
                ); ?>
            </div>
        </div>
    </div>
    <!-- /.col -->
    <div class="col-sm-12 col-lg-4">
        <div class="box box-widget">
            <div class="box-header with-border bg-aqua">
                <h3 class="box-title"><?= $model->getAttributeLabel('image'); ?></h3>
            </div>
            <div class="box-body">
                <?= $form->field($model, 'new_image')->widget(FileInput::classname(), [
                    'options' => ['accept' => 'image/*'],
                    'pluginOptions' => [
                        'showCaption'           => false,
                        'showRemove'            => false,
                        'showUpload'            => false,
                        'browseClass'           => 'btn btn-primary btn-block',
                        'browseIcon'            => '<i class="glyphicon glyphicon-camera"></i>',
                        'previewFileType'       => 'image',
                        'initialPreview'        => $model->imageFile,
                        'initialPreviewAsData'  => true
                    ]
                ])->label($model->getAttributeLabel('image')); ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
