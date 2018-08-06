<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\Blacklist */

$this->title = $model->user->fullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', "Черный список"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>&nbsp;</h1>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]); ?>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-4">
                <div class="box box-widget widget-user-2">
                    <div class="widget-user-header bg-aqua">
                        <div class="widget-user-image">
                            <?= $model->user->userPhoto; ?>
                        </div>
                        <!-- /.widget-user-image -->
                        <h3 class="widget-user-username"><?= $model->user->fullName ?></h3>
                        <h5 class="widget-user-desc"><?= $model->user->email ?><br /><?= $model->user->phone ?></h5>
                    </div>
                    <div class="box-footer text-center">
                        <?= Html::a('Редактировать', ['/admin/user/update', 'id' => $model->user->id], ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
                <?php if ($model->created_by && intval($model->created_by)) : ?>
                <div class="box box-widget">
                    <div class="box-header bg-aqua">
                        <?= Yii::t('app', "Информация о блокировке"); ?>
                    </div>
                    <div class="box-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'attribute' => 'created_by',
                                    'value' => ($model->creator) ? $model->creator->fullName : Yii::t('app', "Администратор")
                                ],
                                [
                                    'attribute' => 'updated_by',
                                    'value' => ($model->updater) ? $model->updater->fullName : Yii::t('app', "Администратор")
                                ]
                            ],
                        ]) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-8">
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua">
                        <?= Yii::t('app', "Информация о блокировке"); ?>

                        <div class="box-tools">
                            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
                            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', "Подтвердите удаление"),
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'add_comment',
                                'created_at:datetime',
                                'updated_at:datetime',
                                'status' => [
                                    'attribute' => 'status',
                                    'value' => function ($data) {
                                        return key_exists($data->status, $data->statusList) ? $data->statusList[$data->status] : false;
                                    }
                                ],
                                'add_type' => [
                                    'attribute' => 'add_type',
                                    'value' => function ($data) {
                                        return key_exists($data->add_type, $data->typesList) ? $data->typesList[$data->add_type] : false;
                                    }
                                ],
                                'description',
                                'cancel_comment',
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
