<?php

use app\modules\admin\models\AuthItem;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$yesno = Yii::$app->params['yesno'];

?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::$app->mv->gt('Информация', [], false) ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'second_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'phone')->textInput() ?>
                <?= $form->field($model, 'gender')->dropDownList($model->genders) ?>

            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
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
                <h4><?= Yii::$app->mv->gt('Роли пользователя', [], false); ?></h4>
                <?= Html::checkboxList('roles',
                    array_keys(ArrayHelper::map(Yii::$app->authManager->getRolesByUser($model->id), 'name', 'description')),
                    ArrayHelper::map(AuthItem::find()->orderBy('description ASC')->all(), 'name', 'description'),
                    [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            $check = $checked ? ' checked="checked"' : '';
                            return "<div class=\"col-sm-4\"><label><input type=\"checkbox\" name=\"$name\" value=\"$value\"$check><span class='role_label'>$label</span></label></div>";
                        }
                    ]
                ); ?>
                <br><br>
                <?= $form->field($model, 'type')->dropDownList($model->types) ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>
<div class="row">
    <!-- /.col -->
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::$app->mv->gt('Настройки', [], false) ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status')->dropDownList($model->statuses) ?>
                        <?= $form->field($model, 'blocked_reason',
                            ['options' => ['style' => ['display' => $model->status == $model::STATUS_BLOCKED ? 'block' : 'none']]])->textInput() ?>
                    </div>
                    <div class="col-sm-6">
                        <?php if (!empty($model->created_at)): ?>
                            <?= $form->field($model, 'created_at')->textInput([
                                'value' => Yii::$app->formatter->asDatetime($model->created_at),
                                'disabled' => 'disabled'
                            ]) ?>
                        <?php endif; ?>
                        <?php if (!empty($model->approved_at)): ?>
                            <?= $form->field($model, 'approved_at')->textInput([
                                'value' => Yii::$app->formatter->asDatetime($model->approved_at),
                                'disabled' => 'disabled'
                            ]) ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6">
                        <?php if (!empty($model->updated_at)): ?>
                            <?= $form->field($model, 'updated_at')->textInput([
                                'value' => Yii::$app->formatter->asDatetime($model->updated_at),
                                'disabled' => 'disabled'
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
        <!-- /.box -->
    </div>
</div>

<?php ActiveForm::end(); ?>
