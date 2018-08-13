<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\Taxi */

$this->title = Yii::$app->mv->gt('Заказ №{id}',['id' => $model->id],false);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Заказы такси',[],false), 'url' => ['index']];
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
                <div class="box box-widget">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <?= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    'user_id' => [
                                        'attribute' => 'user_id',
                                        'value' => $model->user ? $model->user->fullName : false
                                    ],
                                    'address',
                                    'checkpoint' => [
                                        'attribute' => 'checkpoint',
                                        'value' => $model->checkPoint ? $model->checkPoint->title : false
                                    ],
                                    'created_at:datetime',
                                    'updated_at:datetime'
                                ]
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-12 text-center">
                                <?= Html::a('Редактировать', [
                                    'update',
                                    'id' => $model->id
                                ], ['class' => 'btn btn-primary']) ?>
                                <?= Html::a('Удалить', [
                                    'delete',
                                    'id' => $model->id
                                ], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Вы действительно хотите удалить пользователя?',
                                        'method' => 'post',
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
