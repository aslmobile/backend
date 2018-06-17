<?php

use app\modules\admin\models\Lang;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Translations */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::$app->mv->gt('Data', [], false) ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="padding: 10px 0">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 15px;">
                        <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Data', [], false) ?></a>
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
                                <?= $form->field($model, 'trans_key')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'original_val')->textarea() ?>

                                <?= $form->field($model, 'val')->textarea() ?>

                                <?= $form->field($model, 'descr')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

                            </div>
                            <div class="col-sm-6"></div>
                        </div>
                    </div>
                    <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                        <div class="tab-pane" id="top-<?= $k ?>">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'val_' . $k)->label($model->getAttributeLabel('val').' '.$v); ?>
                                </div>
                                <div class="col-sm-6"></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
