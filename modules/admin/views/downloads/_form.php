<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use alexantr\elfinder\InputFile;
use alexantr\tinymce\TinyMCE as TTinyMCE;
use alexantr\elfinder\TinyMCE as ETinyMCE;
use app\modules\admin\models\Lang;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Downloads */
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
                <a data-toggle="tab" href="#top">Data</a>
            </li>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'category_id')->dropDownList(\app\modules\admin\models\Category::fromFilterValue()); ?>

                        <?= $form->field($model, 'sort')->textInput() ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status')->dropDownList($model->statuses) ?>

                        <?= $form->field($model, 'source')->widget(InputFile::className(), [
                            'buttonText' => Yii::$app->mv->gt('Select', [], false),
                            'options' => [
                                'language' => 'en',
                                'class' => 'form-control',
                            ],
                            'clientRoute' => 'el-finder/input',
                            'filter' => ['application/pdf'],
                        ]); ?>
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
