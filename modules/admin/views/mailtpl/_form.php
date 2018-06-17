<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use zxbodya\yii2\elfinder\ElFinderInput;
use zxbodya\yii2\elfinder\ElFinderWidget;
use zxbodya\yii2\tinymce\TinyMce;
use zxbodya\yii2\elfinder\TinyMceElFinder;
use app\modules\admin\models\Lang;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Mailtpl */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>

	<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
	
	<div class="card">
		<div class="card-head style-primary">
			<header><i class="fa fa-edit"></i> <?= Html::encode($this->title) ?></header>
			<div class="tools">
				<?= Html::submitButton('<i class="fa fa-plus"></i>', ['class' => 'btn btn-floating-action btn-default-light']) ?>
				<?= Html::a('<i class="fa fa-reply"></i>', ['index'], ['class' => 'btn btn-floating-action btn-default-light']) ?>
			</div>
		</div>
		
		<div class="card-head">
			<ul class="nav nav-tabs" data-toggle="tabs">
				<li class="active"><a href="#tab1">Данные</a></li>
				<?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
					<li><a href="#<?= $k ?>"><?= $v ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="card-body tab-content">
			<div class="tab-pane active" id="tab1">
				<div class="card-body floating-label">
					<div class="row">
						<div class="col-sm-6">
<?= $form->field($model, 'type', ['template' => '{input}{label}{error}{hint}'])->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'title', ['template' => '{input}{label}{error}{hint}'])->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'descr')->widget(
	TinyMce::className(),
	[
		'fileManager' => [
			'class' => TinyMceElFinder::className(),
			'connectorRoute' => 'el-finder/connector',
		],
	]
) ?>	

</div>
						<div class="col-sm-6">

						</div>
					</div>
					<div class="form-group">
					
					</div>
				</div>
			</div>	
			<?php foreach (Lang::getBehaviorsList() as $k => $v) { ?>
				<div class="tab-pane" id="<?= $k ?>">
					<div class="card-body floating-label">
						<div class="row">
							<div class="col-sm-6">
								<?= $form->field($model, 'title_'.$k, ['template' => '{input}{label}{error}{hint}'])->textInput(['maxlength' => true]) ?>


								<?= $form->field($model, 'descr_'.$k)->widget(
									TinyMce::className(),
									[
										'fileManager' => [
											'class' => TinyMceElFinder::className(),
											'connectorRoute' => 'el-finder/connector',
										],
									]
								) ?>	

							</div>
							<div class="col-sm-6">

							</div>
						</div>
						<div class="form-group">
						
						</div>
					</div>
				</div>
			<?php } ?>
		</div>	
		
		<div class="card-actionbar">
			<div class="card-actionbar-row">
				<?= Html::submitButton($model->isNewRecord ? 'Добавить запись' : 'Сохранить данные', ['class' => 'btn btn-flat btn-primary ink-reaction']) ?>
			</div>
		</div>
	</div>
	<em class="text-caption"><?= Html::encode($this->title) ?></em>	

<?php ActiveForm::end(); ?>