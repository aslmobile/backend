<?php

use app\modules\admin\models\Products;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Products */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
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
                            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                            <?= Html::a('Remove', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                ],
                            ]) ?>
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
                                        'category_id' => [
                                            'attribute' => 'category_id',
                                            'value' => function ($data) {
                                                if (isset($data->category)) {
                                                    return $data->category->title;
                                                }
                                                return null;
                                            },
                                        ],
//                                        'small_image' => [
//                                            'attribute' => 'small_image',
//                                            'value' => function($data){
//                                                if (!empty($data->small_image)) {
//                                                    return $data->small_image;
//                                                }
//                                                return null;
//                                            },
//                                            'format' => ['image', ['width' => '300']],
//                                        ],
//                                        'image' => [
//                                            'attribute' => 'image',
//                                            'value' => function($data){
//                                                if (!empty($data->image)) {
//                                                    return $data->image;
//                                                }
//                                                return null;
//                                            },
//                                            'format' => ['image', ['width' => '300']],
//                                        ],
                                        'status' => [
                                            'attribute' => 'status',
                                            'value' => function ($data) {
                                                return key_exists($data->status, $data->statuses) ? $data->statuses[$data->status] : false;
                                            },
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
