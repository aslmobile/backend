<?php
/**
 * @var \app\modules\admin\models\City $model
 * @var $this yii\web\View
 * @var $model app\models\Countries
 * @var $form yii\widgets\ActiveForm
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
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
            <?php foreach (\app\modules\admin\models\Lang::getBehaviorsList() as $k => $v) { ?>
                <li>
                    <a data-toggle="tab" href="#top-<?= $k ?>" style="max-height: 42px;"><?= ucfirst($v) ?></a>
                </li>
            <?php } ?>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            </div>
            <?php foreach (\app\modules\admin\models\Lang::getBehaviorsList() as $k => $v) { ?>
                <div class="tab-pane fade" id="top-<?= $k ?>">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'title_' . $k)->label($model->getAttributeLabel('title') . ' ' . $v); ?>
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
