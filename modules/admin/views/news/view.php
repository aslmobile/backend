<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = $model->title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->mv->gt('Статьи',[],false),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
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
                                            'value' => function($data){
                                                if (!empty($data->image)) {
                                                    return $data->image;
                                                }
                                                return null;
                                            },
                                            'format' => ['image', ['width' => '200']],
                                        ],
                                        'short_description:html',
                                        [
                                            'attribute' => 'category_id',
                                            'value' => function($model){
                                                return !empty($model->category)? $model->category->title : null;
                                            }
                                        ],
                                        [
                                            'attribute' => 'status',
                                            'value' => key_exists($model->status, $model->statuses)? $model->statuses[$model->status] : null,
                                        ],
                                        [
                                            'attribute' => 'url',
                                            'format' => 'html',
                                            'value' => Html::a($model->url, ['/main/news/article','url' => $model->url], ['target'=>'_blank']),
                                        ],
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
