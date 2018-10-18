<?php

use app\modules\admin\models\Lang;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Legal */
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
                <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Информация', [], false); ?></a>
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
                        <?= $form->field($model, 'type')->dropDownList($model->typesList); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'content')->widget(\unclead\multipleinput\MultipleInput::class, [
                            'data' => $model->content,
                            'sortable' => true,
                            'addButtonPosition' => \unclead\multipleinput\MultipleInput::POS_FOOTER,
                            'allowEmptyList' => true,
                            'columns' => [
                                [
                                    'name' => 'weight',
                                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                                    'title' => Yii::t('app', "Порядок"),
                                    'options' => ['type' => 'number', 'step' => 1, 'style' => 'width:70px;']
                                ],
                                [
                                    'name' => 'title',
                                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                                    'title' => Yii::t('app', "Заголовок"),
                                ],
                                [
                                    'name' => 'description',
                                    'type' => 'textArea',
                                    'title' => Yii::t('app', "Содержимое"),
                                ],
                            ],
                        ])->label(Yii::t('app', "Содержимое")); ?>
                    </div>
                </div>
            </div>
            <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                <div class="tab-pane fade" id="top-<?= $k ?>">
                    <div class="row">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'title_' . $k)->textInput()
                                ->label($model->getAttributeLabel('title') . ' ' . $v); ?>
                            <?php $attribute =  'content_' . $k ?>
                            <?= $form->field($model, $attribute)
                                ->widget(\unclead\multipleinput\MultipleInput::class, [
                                    'data' => json_decode($model->$attribute, true),
                                    'sortable' => true,
                                    'addButtonPosition' => \unclead\multipleinput\MultipleInput::POS_FOOTER,
                                    'allowEmptyList' => true,
                                    'columns' => [
                                        [
                                            'name' => 'weight',
                                            'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                                            'title' => Yii::t('app', "Порядок"),
                                            'options' => ['type' => 'number', 'step' => 1, 'style' => 'width:70px;']
                                        ],
                                        [
                                            'name' => 'title',
                                            'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                                            'title' => Yii::t('app', "Заголовок"),
                                        ],
                                        [
                                            'name' => 'description',
                                            'type' => 'textArea',
                                            'title' => Yii::t('app', "Содержимое"),
                                        ],
                                    ],
                                ]); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
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
