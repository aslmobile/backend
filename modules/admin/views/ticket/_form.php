<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Ticket */
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
                <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Data', [], false); ?></a>
            </li>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-6">

                        <?= $form->field($model, 'status')->dropDownList($model::statusLabels()) ?>

                        <?= $form->field($model, 'user_id')->widget(\kartik\select2\Select2::classname(), [
                            'data' => \yii\helpers\ArrayHelper::map
                            (\app\modules\admin\models\User::find()
                                ->select(['id', 'name' => 'CONCAT(phone, \' \', first_name, \' \', second_name)'])
                                ->where(['type' => \app\modules\admin\models\User::TYPE_DRIVER])->asArray()->all(),
                                'id', 'name'),
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'attribute' => 'user_id',
                            'hideSearch' => true,
                            'options' => [
                                'placeholder' => Yii::t('app', "Водитель")
                            ],
                            'pluginOptions' => ['allowClear' => true]
                        ]); ?>

                    </div>
                    <div class="col-sm-6">

                        <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => 0.01, 'min' => 0]) ?>

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
