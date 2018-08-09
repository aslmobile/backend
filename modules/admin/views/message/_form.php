<?php

use app\modules\admin\models\Lang;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\SourceMessage */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::$app->mv->gt('Информация', [], false) ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="padding: 10px 0">
                <ul class="nav nav-tabs">
                    <li class="active" style="margin-left: 15px;">
                        <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Информация', [], false) ?></a>
                    </li>
                    <?php foreach (ArrayHelper::map(Lang::find()->where('lang.default = 0')->all(), 'local', 'flag') as $k => $v) { ?>
                        <li>
                            <a data-toggle="tab" style="max-height: 42px;" href="#top-<?= $k ?>"><?= Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $v, '18x18', ['class' => 'img-circle']) ?></a>
                        </li>
                    <?php } ?>
                </ul>
                <div class="tab-content" style="padding: 10px">
                    <div id="top" class="tab-pane fade in active">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'category')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'message')->textInput(['rows' => 6]) ?>
                            </div>
                        </div>
                    </div>
                    <?php foreach (ArrayHelper::map(Lang::find()->where('lang.default = 0')->all(), 'local', 'flag') as $k => $v) { ?>
                        <div class="tab-pane" id="top-<?= $k ?>">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <?= Html::label('Перевод ' . Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $v, '18x18', ['class' => 'img-circle']), ['class' => 'control-label']) ?>
                                        <?= Html::textInput('message[' . $k . ']', (isset($translations[$k])) ? $translations[$k] : '', ['class' => 'form-control']) ?>
                                    </div>
                                </div>
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
