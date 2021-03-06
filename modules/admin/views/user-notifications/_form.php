<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use alexantr\elfinder\InputFile;
use alexantr\tinymce\TinyMCE as TTinyMCE;
use alexantr\elfinder\TinyMCE as ETinyMCE;
use app\modules\admin\models\Lang;
/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\UserNotifications */
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
                            <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                <li>
                    <a data-toggle="tab" href="#top-<?= $k ?>" style="max-height: 42px;"><?= $v ?></a>
                </li>
                <?php } ?>                    </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-6">
                            <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'source_id')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'message')->widget(TTinyMCE::className(), [
                                'clientOptions' => [
                                    'language_url' => Yii::$app->homeUrl.'tiny_translates/ru.js',
                                    'language' => 'ru',
                                    'plugins' => [
                                        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                        "searchreplace visualblocks visualchars code fullscreen",
                                        "insertdatetime media nonbreaking save table contextmenu directionality",
                                        "template paste textcolor emoticons",
                                    ],
                                    'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor | emoticons",
                                    'file_picker_callback' => ETinyMCE::getFilePickerCallback(['el-finder/tinymce']),
                                ],
                            ]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'sender_id')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>

                            <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
				<div class="tab-pane fade" id="top-<?= $k ?>">
					<div class="row">
					    <div class="col-sm-6">                        <?= $form->field($model, 'message_'.$k)->label($model->getAttributeLabel('message').' '.$v) ; ?>

<?= $form->field($model, 'title_'.$k)->label($model->getAttributeLabel('title').' '.$v) ; ?>

                        

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
