<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use app\components\widgets\Alert;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Watchdog */

$this->title = Yii::$app->mv->gt('Editing {modelClass}: ', [
    'modelClass' => 'Watchdog',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Admin panel',[],false), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Watchdogs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::$app->mv->gt('Editing {modelClass}', [
    'modelClass' => 'Watchdog',
]);
?>

<div class="content-wrapper">
	<section>
		<div class="section-header">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>			
		</div>	
		<div class="section-body contain-xlg">	
		
			<div class="row">
				<div class="col-lg-12">
				
					<?= Alert::widget() ?>

					<?= $this->render('_form', [
						'model' => $model,
					]) ?>
				
				</div>
			</div>
		
		</div>
	</section>
</div>
