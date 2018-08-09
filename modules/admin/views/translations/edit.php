<?php
use app\components\widgets\Alert;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Translations */

$this->title = Yii::$app->mv->gt('Изменить перевод', false);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Переводы'), 'url' => ['index']];
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
        <?= Alert::widget() ?>
        <div class="row">
            <div class="col-lg-12">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>

                <div class="row">
                    <div class="col-md-8">
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
                                <div class="tab-content" style="padding: 10px">
                                    <div class="row">
                                        <div class="col-sm-12">


                                            <?= $form->field($model, 'val')->widget(Select2::classname(), [
                                                'data' => $translations,
                                                'options' => ['placeholder' => Yii::t('app', 'Перевод')],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                ],
                                            ])->label(Yii::t('app', 'Перевод')); ?>

                                            <?= $form->field($model, 'new_val')->textarea()->label(Yii::t('app', 'Новое значение')) ?>

                                        </div>
                                        <div class="col-sm-12"></div>
                                    </div>
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

            </div>
        </div>
    </section>
</div>
