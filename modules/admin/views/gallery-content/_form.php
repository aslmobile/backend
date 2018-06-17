<?php

use app\modules\admin\models\Lang;
use kartik\file\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Group */
/* @var $form yii\widgets\ActiveForm */

$types = Yii::$app->params['content_type'];
$statuses = Yii::$app->params['content_status'];

$image_extensions = explode(',', Yii::$app->params['image_extensions']);
$video_extensions = explode(',', Yii::$app->params['video_extensions']);
$extensions = new JsExpression(
        'var image_extensions = ' . json_encode($image_extensions) . ';
                  var video_extensions = ' . json_encode($video_extensions) . ';
                  ');
$this->registerJs($extensions, View::POS_BEGIN);

?>
<?php $form = ActiveForm::begin(['options' => ['class' => 'form', 'enctype' => 'multipart/form-data', 'id' => 'gallery_content_form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">

    <div class="col-sm-7">

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::$app->mv->gt('Item', [], false) ?></h3>
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
                            <div class="col-sm-5 preview_content">
                                <?php

                                if ($model->type == 1) {
                                    echo \wbraganca\videojs\VideoJsWidget::widget([
                                        'options' => [
                                            'class' => 'video-js vjs-default-skin vjs-big-play-centered',
                                            'controls' => true,
                                            'preload' => 'auto',
                                        ],
                                        'tags' => [
                                            'source' => [
                                                ['src' => Yii::getAlias('@web') . $model->path, 'type' => 'video/' . $model->ext],
                                            ],
                                        ],
                                    ]);
                                } else {
                                    echo \branchonline\lightbox\Lightbox::widget([
                                        'files' => [
                                            [
                                                'thumb' => Yii::getAlias('@web') . $model->path,
                                                'original' => Yii::getAlias('@web') . $model->path,
                                                'title' => 'optional title',
                                            ],
                                        ],
                                    ]);
                                }

                                ?>
                            </div>
                            <div class="col-sm-7">
                                <?= $form->field($model, 'path')->widget(FileInput::classname(), [
                                    'pluginOptions' => [
                                        'showUpload' => false,
                                    ],
                                    'pluginEvents' => [
                                        'change' => "function() { 
                                            var fake_path = $(this).val();
                                            ext = fake_path.split('.')[fake_path.split('.').length - 1];
                                            if(_.findIndex(image_extensions, function(o) { return o == ext; }) >= 0){
                                                $('#gallerycontent-type').attr('value', 0);
                                            }else if(_.findIndex(video_extensions, function(o) { return o == ext; }) >= 0){
                                                $('#gallerycontent-type').attr('value', 1);
                                            }
                                            var form = $('#gallery_content_form');
                                            form.yiiActiveForm('validateAttribute', 'gallerycontent-status');
                                        }",
                                    ],
                                ]); ?>
                                <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                            </div>

                            <div class="col-sm-4">
                                <?= $form->field($model, 'status')->dropdownList($statuses); ?>
                            </div>

                            <div class="col-sm-4">
                                <?= $form->field($model, 'gallery_id')->dropdownList(ArrayHelper::map(\app\modules\admin\models\Gallery::find()->asArray()->all(), 'id', 'title')); ?>
                            </div>
                        </div>

                        <?= $form->field($model, 'description')->textarea(['maxlength' => true, 'rows' => 6]) ?>
                    </div>
                    <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                        <div class="tab-pane" id="verif-<?= $k ?>">
                            <?= $form->field($model, 'title_' . $k)
                                ->textInput(['maxlength' => true])
                                ->label($model->getAttributeLabel('title') . ' ' . $v) ?>
                            <?= $form->field($model, 'description_' . $k)
                                ->textarea(['maxlength' => true, 'rows' => 6])
                                ->label($model->getAttributeLabel('description') . ' ' . $v) ?>
                        </div>
                    <?php } ?>
                    <div class="box-footer clearfix text-right">
                        <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-5">
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
