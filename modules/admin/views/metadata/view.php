<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Metadata */

$this->title = $model->title;
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
		
			<h1><?= Html::encode($this->title) ?></h1>

			<p>
                <?= \app\components\widgets\FormButtons::widget(['model' => $model, 'topButtons' => true]) ?>
			</p>
		
			<div class="row">
				<div class="col-lg-12">

					<?= DetailView::widget([
						'model' => $model,
						'attributes' => [
				            'id',
            'data_id',
            'created_at',
            'updated_at',
            'data_type',
            'title',
            'description',
            'bagagge:ntext',
						],
					]) ?>

				</div>
			</div>
		
		</div>
	</section>
</div>
