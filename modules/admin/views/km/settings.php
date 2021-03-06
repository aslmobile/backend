<?php

use alexantr\elfinder\TinyMCE as ETinyMCE;
use alexantr\tinymce\TinyMCE as TTinyMCE;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Km */
/* @var $form yii\widgets\ActiveForm */
/* @var $routes array */
/* @var $days array */

$this->title = Yii::t('app', 'Настройки');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Бесплатные КМ'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="content-wrapper">
    <section class="content-header">
        <?= Html::tag('h1', $this->title) ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
        <?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= $this->title; ?></h3>
            </div>
            <div class="box-body">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#settings"><?= Yii::t('app', 'Настройки'); ?></a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#accumulation"><?= Yii::t('app', 'Настройки накоплений'); ?></a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#waste"><?= Yii::t('app', 'Настройки трат'); ?></a>
                    </li>
                </ul>
                <div class="tab-content" style="padding: 15px;">
                    <div id="settings" class="tab-pane fade in active">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'description')->widget(TTinyMCE::className(), [
                                    'clientOptions' => [
                                        'language_url' => Yii::$app->homeUrl . 'tiny_translates/ru.js',
                                        'language' => Yii::$app->controller->getOldLangAssoc(Yii::$app->language),
                                        'plugins' => [
                                            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                            "searchreplace visualblocks visualchars code fullscreen",
                                            "insertdatetime media nonbreaking save table contextmenu directionality",
                                            "template paste textcolor emoticons",
                                        ],
                                        'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor | emoticons",
                                        'file_picker_callback' => ETinyMCE::getFilePickerCallback(['el-finder/tinymce']),
                                    ]
                                ]) ?>
                            </div>
                            <div class="col-sm-6">

                            </div>
                        </div>
                    </div>
                    <div id="accumulation" class="tab-pane fade">
                        <?= $form->field($model, 'settings_accumulation')->widget(\unclead\multipleinput\MultipleInput::class, [
                            'data' => $model->settings_accumulation,
                            'sortable' => true,
                            'addButtonPosition' => \unclead\multipleinput\MultipleInput::POS_FOOTER,
                            'allowEmptyList' => true,
                            'columns' => [
                                [
                                    'name' => 'route',
                                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_DROPDOWN,
                                    'title' => Yii::t('app', "Маршрут"),
                                    'items' => $routes,
                                    'options' => ['class' => '', 'required' => true]
                                ],
                                [
                                    'name' => 'days',
                                    'type' => \kartik\select2\Select2::class,
                                    'title' => Yii::t('app', "Дни"),
                                    'value' => function ($data) {
                                        return isset($data['days']) ? $data['days'] : [];
                                    },
                                    'options' => [
                                        'data' => $days,
                                        'options' => ['multiple' => true, 'required' => true],
                                        'pluginOptions' => [
                                            'closeOnSelect' => false,
                                        ],
                                    ]
                                ],
                                [
                                    'name' => 'from',
                                    'type' => \kartik\time\TimePicker::class,
                                    'title' => Yii::t('app', "С"),
                                    'options' => [
                                        'options' => ['required' => true],
                                        'pluginOptions' => [
                                            'showMeridian' => false,
                                        ],
                                    ]
                                ],
                                [
                                    'name' => 'to',
                                    'type' => \kartik\time\TimePicker::class,
                                    'title' => Yii::t('app', "по"),
                                    'options' => [
                                        'options' => ['required' => true],
                                        'pluginOptions' => [
                                            'showMeridian' => false,
                                        ],
                                    ]
                                ],
                                [
                                    'name' => 'rate',
                                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                                    'title' => Yii::t('app', "Ставка на 1 км"),
                                    'options' => ['type' => 'number', 'step' => 0.01, 'required' => true]
                                ]
                            ],
                        ])->label(Yii::t('app', "Настройки накоплений")); ?>
                    </div>
                    <div id="waste" class="tab-pane fade">
                        <?= $form->field($model, 'settings_waste')->widget(\unclead\multipleinput\MultipleInput::class, [
                            'data' => $model->settings_waste,
                            'sortable' => true,
                            'addButtonPosition' => \unclead\multipleinput\MultipleInput::POS_FOOTER,
                            'allowEmptyList' => true,
                            'columns' => [
                                [
                                    'name' => 'route',
                                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_DROPDOWN,
                                    'title' => Yii::t('app', "Маршрут"),
                                    'items' => $routes,
                                    'options' => ['class' => '', 'required' => true]
                                ],
                                [
                                    'name' => 'days',
                                    'type' => \kartik\select2\Select2::class,
                                    'title' => Yii::t('app', "Дни"),
                                    'value' => function ($data) {
                                        return isset($data['days']) ? $data['days'] : [];
                                    },
                                    'options' => [
                                        'data' => $days,
                                        'options' => ['multiple' => true, 'required' => true],
                                        'pluginOptions' => [
                                            'closeOnSelect' => false,
                                        ],
                                    ]
                                ],
                                [
                                    'name' => 'from',
                                    'type' => \kartik\time\TimePicker::class,
                                    'title' => Yii::t('app', "С"),
                                    'options' => [
                                        'options' => ['required' => true],
                                        'pluginOptions' => [
                                            'showMeridian' => false,
                                        ],
                                    ]
                                ],
                                [
                                    'name' => 'to',
                                    'type' => \kartik\time\TimePicker::class,
                                    'title' => Yii::t('app', "по"),
                                    'options' => [
                                        'options' => ['required' => true],
                                        'pluginOptions' => [
                                            'showMeridian' => false,
                                        ],
                                    ]
                                ],
                            ],
                        ])->label(Yii::t('app', "Настройки трат")); ?>
                    </div>
                </div>
            </div>
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
    </section>
</div>
