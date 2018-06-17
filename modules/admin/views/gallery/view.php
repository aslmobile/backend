<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Gallery */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Galleries', [], 0), 'url' => ['index']];
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
                                        [
                                            'attribute' => 'preview',
                                            'format' => 'html',
                                            'value' => function ($data) {
                                                return Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $data->preview, '50x50', ['class' => 'img-circle width-1']);
                                            },
                                        ],
                                        'title',
                                        [
                                            'attribute' => 'status',
                                            'value' => function ($model) {
                                                return Yii::$app->params['status'][$model->status];
                                            },
                                        ],
                                        'image_count',
                                        'video_count',
                                        'created_at:datetime',
                                        [
                                            'attribute' => 'created_by',
                                            'value' => function ($model) {
                                                $user = \app\models\User::findOne($model->created_by);

                                                return $user ? $user->last_name : '';
                                            },
                                        ],
                                        'updated_at:datetime',
                                        [
                                            'attribute' => 'updated_by',
                                            'value' => function ($model) {
                                                $user = \app\models\User::findOne($model->updated_by);

                                                return $user ? $user->last_name : '';
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
