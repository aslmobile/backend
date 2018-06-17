<?php

use app\modules\admin\models\Lang;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Gallery */
/* @var $form yii\widgets\ActiveForm */

$statuses = Yii::$app->params['status'];

?>
<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">

    <div class="col-sm-6">

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::$app->mv->gt('Gallery', [], false) ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="padding: 10px 0">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 15px;">
                        <a data-toggle="tab" href="#verif"><?= Yii::$app->mv->gt('Data', [], false) ?></a>
                    </li>
                    <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                        <li>
                            <a data-toggle="tab" href="#verif-<?= $k ?>" style="max-height: 42px;"><?= $v ?></a>
                        </li>
                    <?php } ?>
                </ul>
                <div class="tab-content" style="padding: 10px">
                    <div class="tab-pane active" id="verif">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'preview')->widget(alexantr\elfinder\InputFile::className(), [
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
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'status')->dropdownList($statuses); ?>
                            </div>
                        </div>
                    </div>
                    <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                        <div class="tab-pane" id="verif-<?= $k ?>">
                            <?= $form->field($model, 'title_' . $k)
                                ->textInput(['maxlength' => true])
                                ->label($model->getAttributeLabel('title') . ' ' . $v) ?>
                        </div>
                    <?php } ?>
                    <div class="box-footer clearfix text-right">
                        <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::$app->mv->gt('Edited info', [], false) ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <?php if (!empty($model->created_at)): ?>
                            <?= $form->field($model, 'created_at')->textInput([
                                'value' => Yii::$app->formatter->asDatetime($model->created_at),
                                'disabled' => 'disabled',
                            ]) ?>
                            <?php
                            $created_user = \app\models\User::getUserById($model->created_by);
                            ?>
                            <?= $form->field($model, 'created_by')->textInput([
                                'value' => (!empty($created_user)) ? $created_user->name : '',
                                'disabled' => 'disabled',
                            ]) ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6">
                        <?php if (!empty($model->updated_at)): ?>
                            <?= $form->field($model, 'updated_at')->textInput([
                                'value' => Yii::$app->formatter->asDatetime($model->updated_at),
                                'disabled' => 'disabled',
                            ]) ?>
                            <?php
                            $updated_user = \app\models\User::getUserById($model->updated_by);
                            ?>
                            <?= $form->field($model, 'updated_by')->textInput([
                                'value' => (!empty($updated_user)) ? $updated_user->name : '',
                                'disabled' => 'disabled',
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php ActiveForm::end(); ?>
