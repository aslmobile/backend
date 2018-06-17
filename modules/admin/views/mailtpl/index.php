<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\MailtplSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Шаблоны писем');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Admin panel',[],false), 'url' => ['/admin']];
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

			<div class="card">

				<div class="card-head style-primary">
					<header><i class="fa fa-table"></i> <?= Html::encode($this->title) ?></header>
					<div class="tools">
						<?= Html::a('<i class="fa fa-plus"></i>', ['create'], ['class' => 'btn btn-floating-action btn-default-light']) ?>
						<?= Html::a('<i class="fa fa-fire"></i>', [''], [
							'class' => 'btn ink-reaction btn-floating-action btn-default-light',
							'onclick'=>"
								var keys = $('#grid').yiiGridView('getSelectedRows');
								if (keys!='') {
									if (confirm('Вы уверены, что хотите удалить выбранные элементы?')) {
										$.ajax({
											type : 'POST',
											data: {keys : keys},
											success : function(data) {}
										});
									}
								}
								return false;
							",
						]) ?>
					</div>
				</div>

				<div class="card-body">

					<div class="row">
						<div class="col-lg-12">
							<h4></h4>
						</div>
						<div class="col-lg-12">
							
														<?= GridView::widget([
									'dataProvider' => $dataProvider,
									'id' => 'grid',
									'layout'=>"
										<div class='dataTables_info'>{summary}</div>
										<div class='card'>
											<div class='card-body no-padding'>
												<div class='table-responsive no-margin'>{items}</div>
											</div>
										</div>
										<div class='dataTables_paginate paging_simple_numbers'>{pager}</div>
									",		
									'tableOptions' => [
										'class' => 'table table-striped no-margin table-hover',
									],	
									'filterModel' => $searchModel,
        'columns' => [
										//['class' => 'yii\grid\SerialColumn'],
										['class' => 'yii\grid\CheckboxColumn'],

								
								
								/***GENERATED! DO NOT DELETE THIS COMMENT***/
									
									'id',

									'type',
									'title',

									[
										'attribute' => 'descr',
										'filter' => false,
										'format' => 'html',
										'content' => function($data) {
											return $data->descr;
										}
									],


								/***GENERATED! DO NOT DELETE THIS COMMENT***/




							           

										[
											'class' => 'yii\grid\ActionColumn',
											'template' => '{view} {update} {delete}',
											'buttons' => [
												'view' => function ($url, $model) {
													return Html::a('<button type="button" class="btn btn-icon-toggle"><i class="fa fa-search"></i></button>', $url);
												},											
												'update' => function ($url, $model) {
													return Html::a('<button type="button" class="btn btn-icon-toggle"><i class="fa fa-pencil"></i></button>', $url);
												},											
												'delete' => function ($url, $model) {
													return Html::a('<button type="button" class="btn btn-icon-toggle"><i class="fa fa-trash-o"></i></button>', $url, ['data-confirm'=>'Are you sure you want to delete this item?', 'data-method'=>'post', 'data-pjax'=>'0']);
												},
											]
										],
									],
								]); ?>
												
						</div>
					</div>	
				</div>

			</div>

		</div>
	</section>
</div>
