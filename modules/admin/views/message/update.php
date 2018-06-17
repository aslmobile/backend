<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use app\components\widgets\Alert;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\SourceMessage */

$this->title = Yii::$app->mv->gt('Редактирование перевода', [], 0) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Admin panel',[],false), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Переводы', [], 0), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::$app->mv->gt('Редактирование перевода', [], 0);
?>

<div class="content-wrapper">
	<section>
		<div class="content-header">
            <?= Html::tag('h1', $this->title)?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
		</div>
		<div class="content">
			<div class="row">
				<div class="col-lg-12">

					<?= Alert::widget() ?>

					<?= $this->render('_form', [
						'model' => $model,
						'translations' => $translations,
					]) ?>

				</div>
			</div>

		</div>
	</section>
</div>
