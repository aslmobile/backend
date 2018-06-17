<?php

use app\components\widgets\ElFinderInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use zxbodya\yii2\elfinder\ElFinderWidget;
use zxbodya\yii2\tinymce\TinyMce;
use zxbodya\yii2\elfinder\TinyMceElFinder;
use app\modules\admin\models\Lang;
/* @var $this yii\web\View */
/* @var $model app\models\StaticContent */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>

<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Yii::$app->mv->gt('Top content',[],false)?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="padding: 10px 0">
                    <ul class="nav nav-tabs">
                        <li class="active" style="margin-left: 15px;">
                            <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Data',[],false)?></a>
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
                                    <?= $form->field($model, 'fan_title')->textInput(['maxlength' => true]) ?>

                                    <?= $form->field($model, 'fan_title_color')->textInput(['maxlength' => true,'class'=>'jscolor form-control']) ?>

                                    <?php
                                    if(!empty($model->fan_icon)){
                                        echo Html::img($model->fan_icon, ['style' => 'max-height: 50px;']);
                                    }
                                    ?>
                                    <?= $form->field($model, 'fan_icon')->widget(
                                        ElFinderInput::className(),
                                        [
                                            'connectorRoute' => 'el-finder/connector',
                                        ]
                                    ) ?>


                                    <?php
                                    if(!empty($model->fan_image)){
                                        echo Html::img($model->fan_image, ['style' => 'max-height: 50px;']);
                                    }
                                    ?>
                                    <?= $form->field($model, 'fan_image')->widget(
                                        ElFinderInput::className(),
                                        [
                                            'connectorRoute' => 'el-finder/connector',
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'fan_tooltip')->widget(
                                        TinyMce::className(),
                                        [
                                            'fileManager' => [
                                                'class' => TinyMceElFinder::className(),
                                                'connectorRoute' => 'el-finder/connector',
                                            ],
                                        ]
                                    ) ?>
                                </div>
                            </div>
                        </div>
                        <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                            <div class="tab-pane fade" id="top-<?= $k ?>">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'fan_title_'.$k) ; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'fan_tooltip_'.$k)->widget(
                                            TinyMce::className(),
                                            [
                                                'fileManager' => [
                                                    'class' => TinyMceElFinder::className(),
                                                    'connectorRoute' => 'el-finder/connector',
                                                ],
                                            ]
                                        ) ; ?>
                                    </div>
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
            <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Yii::$app->mv->gt('CRUSH',[],false)?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="padding: 10px 0">
                    <ul class="nav nav-tabs">
                        <li class="active" style="margin-left: 15px;">
                            <a data-toggle="tab" href="#cruch"><?= Yii::$app->mv->gt('Data',[],false)?></a>
                        </li>
                        <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                            <li>
                                <a data-toggle="tab" href="#cruch-<?= $k ?>" style="max-height: 42px;"><?= $v ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content" style="padding: 10px">
                        <div id="cruch" class="tab-pane fade in active">
                            <?= $form->field($model, 'crush_tooltip')->widget(
                                TinyMce::className(),
                                [
                                    'fileManager' => [
                                        'class' => TinyMceElFinder::className(),
                                        'connectorRoute' => 'el-finder/connector',
                                    ],
                                ]
                            ) ?>
                        </div>
                        <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                            <div class="tab-pane fade" id="cruch-<?= $k ?>">
                                <?= $form->field($model, 'crush_tooltip_'.$k)->widget(
                                    TinyMce::className(),
                                    [
                                        'fileManager' => [
                                            'class' => TinyMceElFinder::className(),
                                            'connectorRoute' => 'el-finder/connector',
                                        ],
                                    ]
                                ) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix text-right">
                    <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Yii::$app->mv->gt('Verified Athlete Benefit',[],false)?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="padding: 10px 0">
                    <ul class="nav nav-tabs">
                        <li class="active" style="margin-left: 15px;">
                            <a data-toggle="tab" href="#"><?= Yii::$app->mv->gt('Data',[],false)?></a>
                        </li>
                        <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                            <li>
                                <a data-toggle="tab" href="#verif-<?= $k ?>" style="max-height: 42px;"><?= $v ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content" style="padding: 10px">
                        <div id="verif" class="tab-pane fade in active">
                            <?= $form->field($model, 'verified_tooltip')->widget(
                                TinyMce::className(),
                                [
                                    'fileManager' => [
                                        'class' => TinyMceElFinder::className(),
                                        'connectorRoute' => 'el-finder/connector',
                                    ],
                                ]
                            ) ?>
                        </div>
                        <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                            <div class="tab-pane fade" id="verif-<?= $k ?>">
                                <?= $form->field($model, 'verified_tooltip_'.$k)->widget(
                                    TinyMce::className(),
                                    [
                                        'fileManager' => [
                                            'class' => TinyMceElFinder::className(),
                                            'connectorRoute' => 'el-finder/connector',
                                        ],
                                    ]
                                ) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix text-right">
                    <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Yii::$app->mv->gt('Unique Code Information',[],false)?></h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="padding: 10px 0">
                    <ul class="nav nav-tabs">
                        <li class="active" style="margin-left: 15px;">
                            <a data-toggle="tab" href="#code"><?= Yii::$app->mv->gt('Data',[],false)?></a>
                        </li>
                        <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                            <li>
                                <a data-toggle="tab" href="#code-<?= $k ?>" style="max-height: 42px;"><?= $v ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content" style="padding: 10px">
                        <div id="top" class="tab-pane fade in active">
                            <?= $form->field($model, 'unique_code_tooltip')->widget(
                                TinyMce::className(),
                                [
                                    'fileManager' => [
                                        'class' => TinyMceElFinder::className(),
                                        'connectorRoute' => 'el-finder/connector',
                                    ],
                                ]
                            ) ?>
                        </div>
                        <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                            <div class="tab-pane fade" id="code-<?= $k ?>">
                                <?= $form->field($model, 'unique_code_tooltip_'.$k)->widget(
                                    TinyMce::className(),
                                    [
                                        'fileManager' => [
                                            'class' => TinyMceElFinder::className(),
                                            'connectorRoute' => 'el-finder/connector',
                                        ],
                                    ]
                                ) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix text-right">
                    <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
<?php ActiveForm::end(); ?>