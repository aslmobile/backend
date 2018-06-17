<?php

use app\modules\admin\models\Menu;
use app\modules\admin\models\Lang;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Menu */
/* @var $form yii\widgets\ActiveForm */

$menu_query = Menu::find();

if(!$model->isNewRecord){
    $menu_query->andWhere(['!=','id',$model->id]);
}

$menu_items = $menu_query->all();

?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Yii::$app->mv->gt('Меню',[],false)?></h3>
            <div class="box-tools pull-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body" style="padding: 10px 0">
            <ul class="nav nav-tabs">
                <li class="active" style="margin-left: 15px;">
                    <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Данные',[],false)?></a>
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
                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'group')->dropDownList(Menu::getMenuGroups()) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'parent_id')->dropDownList([0 => '(Не выбрано)'] + ArrayHelper::map($menu_items, 'id', 'name')) ?>

                            <?= $form->field($model, 'visible_type')->dropDownList(Menu::getVisibilityTypes()) ?>

                            <?= $form->field($model, 'sort_order')->textInput() ?>
                        </div>
                    </div>
                </div>
                <?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                    <div class="tab-pane fade" id="top-<?= $k ?>">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'name_' . $k)->label($model->getAttributeLabel('name').' '.$v); ?>
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
