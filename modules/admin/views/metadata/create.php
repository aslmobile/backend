<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Metadata */

$this->title = Yii::$app->mv->gt('New Metadata', [], false);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Admin panel',[],false), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Metadatas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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

					<?=  $this->render('_form', [
						'model' => $model,
					]) ?>
				
				</div>
			</div>
		
		</div>
	</section>
</div>
