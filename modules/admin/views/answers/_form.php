<?php

use app\modules\admin\models\Lang;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Answers */
/* @var $form yii\widgets\ActiveForm */

$id = 0;

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
                        <?= $form->field($model, 'answer')->widget(\unclead\multipleinput\MultipleInput::class, [
                            'data' => $model->answer,
                            'sortable' => false,
                            'addButtonPosition' => \unclead\multipleinput\MultipleInput::POS_FOOTER,
                            'allowEmptyList' => true,
                            'min' => 1,
                            'columns' => [
                                [
                                    'name' => 'id',
                                    'type' => \unclead\multipleinput\TabularColumn::TYPE_HIDDEN_INPUT,
                                    'value' => function ($data) use (&$id) {
                                        return ++$id;
                                    },
                                ],
                                [
                                    'name' => 'weight',
                                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                                    'title' => Yii::t('app', "Порядок"),
                                    'options' => ['type' => 'number', 'step' => 1, 'style' => 'width:70px;']
                                ],
                                [
                                    'name' => 'answer',
                                    'type' => 'textArea',
                                    'title' => Yii::t('app', "Содержимое"),
                                ],
                            ],
                        ])->label(Yii::t('app', "Ответы")); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'type', ['enableAjaxValidation' => true])->dropDownList($model->typesList); ?>
                    </div>
                </div>
            </div>
            <?php $id = 0; ?>
            <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                <div class="tab-pane fade" id="top-<?= $k ?>">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php $attribute = 'answer_' . $k ?>
                            <?= $form->field($model, $attribute)
                                ->widget(\unclead\multipleinput\MultipleInput::class, [
                                    'data' => is_string($model->$attribute)?json_decode($model->$attribute, true):$model->$attribute,
                                    'sortable' => false,
                                    'addButtonPosition' => \unclead\multipleinput\MultipleInput::POS_FOOTER,
                                    'allowEmptyList' => true,
                                    'min' => 1,
                                    'columns' => [
                                        [
                                            'name' => 'id',
                                            'type' => \unclead\multipleinput\TabularColumn::TYPE_HIDDEN_INPUT,
                                            'value' => function ($data) use (&$id) {
                                                return ++$id;
                                            },
                                        ],
                                        [
                                            'name' => 'weight',
                                            'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                                            'title' => Yii::t('app', "Порядок"),
                                            'options' => ['type' => 'number', 'step' => 1, 'style' => 'width:70px;']
                                        ],
                                        [
                                            'name' => 'answer',
                                            'type' => 'textArea',
                                            'title' => Yii::t('app', "Содержимое"),
                                        ],
                                    ],
                                ])->label(Yii::t('app', "Ответы")); ?>
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
