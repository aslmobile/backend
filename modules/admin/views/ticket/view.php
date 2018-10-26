<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Ticket */

$this->title = Yii::$app->mv->gt('Заявка №{title}', ['title' => $model->id], false);
$this->params['breadcrumbs'][] = ['label' => 'Вывод средств', 'url' => ['index']];
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
                            <?php if ($model->status != \app\models\Ticket::STATUS_PAYED) { ?>
                                <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                                <!--                            --><? //= Html::a('Удалить', ['delete', 'id' => $model->id], [
//                                'class' => 'btn btn-danger',
//                                'data' => [
//                                    'confirm' => 'Are you sure you want to delete this item?',
//                                    'method' => 'post',
//                                ],
//                            ]) ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <?= DetailView::widget([
                                    'model' => $model,
                                    'attributes' => [
                                        ['attribute' => 'id'],
                                        [
                                            'attribute' => 'user_id',
                                            'content' => function ($data) {
                                                return !empty($data->user) ?
                                                    $data->user->fullName :
                                                    Yii::t('app', "Удален");
                                            },
                                        ],
                                        [
                                            'attribute' => 'status',
                                            'content' => function ($model) {
                                                return $model->statusLabel;
                                            },
                                        ],
                                        'amount',
                                        [
                                            'attribute' => 'created_at',
                                            'value' => function ($module) {
                                                return Yii::$app->formatter->asDateTime($module->created_at);
                                            },
                                            'format' => 'raw',
                                        ],
                                        [
                                            'attribute' => 'updated_at',
                                            'value' => function ($module) {
                                                return Yii::$app->formatter->asDateTime($module->created_at);
                                            },
                                            'format' => 'raw',
                                        ],
                                        [
                                            'attribute' => 'updated_by',
                                            'content' => function ($data) {
                                                return !empty($data->updated) ?
                                                    $data->updated->fullName :
                                                    Yii::t('app', "Удален");
                                            },
                                        ],
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
