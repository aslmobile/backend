<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator app\modules\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use alexantr\elfinder\InputFile;
use alexantr\tinymce\TinyMCE as TTinyMCE;
use alexantr\elfinder\TinyMCE as ETinyMCE;
use app\modules\admin\models\Lang;
/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<?= "<?php " ?>$form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= "<?=" ?> $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?= "<?=" ?> $this->title; ?>
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
                <a data-toggle="tab" href="#top"><?= Yii::$app->mv->gt('Data',[],false)?></a>
            </li>
            <?php if($generator->multiLang): ?>
                <?= "<?php" ?> foreach (Lang::getBehaviorsList() as $k => $v) { ?>
                <li>
                    <a data-toggle="tab" href="#top-<?= '<?= $k ?>' ?>" style="max-height: 42px;"><?= '<?= $v ?>' ?></a>
                </li>
                <?= "<?php } ?>" ?>
            <?php endif; ?>
        </ul>

        <div class="tab-content" style="padding: 10px">
            <div id="top" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-sm-6">
                        <?php foreach ($generator->getColumnNames() as $attribute) {
                            if (in_array($attribute, $safeAttributes)) {
                                echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
                            }
                        } ?>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>

            <?php if($generator->multiLang): ?>
                <?= "<?php foreach (Lang::getBehaviorsList() as \$k => \$v) { ?>
				<div class=\"tab-pane fade\" id=\"top-<?= \$k ?>\">
					<div class=\"row\">
					    <div class=\"col-sm-6\">";?>
                        <?php foreach ($generator->getMlColumnNames() as $attribute) {
                            if (in_array($attribute, $safeAttributes)&&$attribute!='id') {
                                echo "<?= " . $generator->generateMlActiveField($attribute.'_\'.$k') ."->label(\$model->getAttributeLabel('$attribute').' '.\$v) ; ?>\n\n";
                            }
                        } ?>
                        <?= "\n
                        </div>
                        <div class=\"col-sm-6\"></div>\n
				    </div>\n
				</div>\n
                <?php } ?>" ?>
            <? endif; ?>

        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix text-right">
        <?= "<?=" ?> Html::submitButton(
            ($model->isNewRecord ?
            Yii::$app->mv->gt('{i} Добавить',['i'=>Html::tag('i','',['class'=>'fa fa-save'])],0) :
            Yii::$app->mv->gt('{i} Сохранить',['i'=>Html::tag('i','',['class'=>'fa fa-save'])],0)),
            ['class' => 'btn btn-success']
        ) ?>
    </div>
</div>

<?= "<?php " ?>ActiveForm::end(); ?>
