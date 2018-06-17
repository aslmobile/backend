<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\SourceMessage */

$this->title = Yii::$app->mv->gt('Новый перевод', [], 0);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Admin panel',[],false), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Переводы', [], 0), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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

					<?=  $this->render('_form', [
						'model' => $model,
						'translations' => $translations,
					]) ?>

				</div>
			</div>

		</div>
	</section>
</div>
