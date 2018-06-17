<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Dynamic */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Страницы',[],0), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if(!$model->image){ $model->image = '/files/images/nophoto.png';}
?>

<div class="content-wrapper">
	<section class="content-header">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
	</section>
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3></h3>
                        <div class="box-tools pull-right">
                            <?= \app\components\widgets\FormButtons::widget(['model' => $model, 'topButtons' => true]) ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <?= DetailView::widget([
                                    'model' => $model,
                                    'attributes' => [
                                        'id',
                                        'title',
                                        [
                                            'attribute' => 'image',
                                            'value' => $model->image,
                                            'format' => ['image', ['height' => '100']],
                                        ],
                                        [
                                            'attribute' => 'url',
                                            'format' => 'html',
                                            'value' => Html::a($model->url, ['/main/default/index','link' => $model->url], ['target'=>'_blank']),
                                        ],
                                        'text:html',
                                        'created_at:datetime',
                                        'updated_at:datetime',
                                    ],
                                ]) ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
