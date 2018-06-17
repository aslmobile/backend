<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\GalleryContent */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Galleries content', [], 0), 'url' => ['index']];
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
            <div class="col-sm-5">
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
                                            'attribute' => 'path',
                                            'format' => 'html',
                                            'contentOptions' => ['class' => 'content_preview'],
                                            'value' => function ($model) {
                                                if ($model->type == 1) {
                                                    return \wbraganca\videojs\VideoJsWidget::widget([
                                                        'options' => [
                                                            'class' => 'video-js vjs-default-skin vjs-big-play-centered',
                                                            'controls' => true,
                                                            'preload' => 'auto',
                                                        ],
                                                        'tags' => [
                                                            'source' => [
                                                                ['src' => Yii::getAlias('@web') . $model->path, 'type' => 'video/' . $model->ext],
                                                            ],
                                                        ],
                                                    ]);
                                                } else {
                                                    return \branchonline\lightbox\Lightbox::widget([
                                                        'files' => [
                                                            [
                                                                'thumb' => Yii::getAlias('@web') . $model->path,
                                                                'original' => Yii::getAlias('@web') . $model->path,
                                                                'title' => 'optional title',
                                                            ],
                                                        ],
                                                    ]);
                                                }
                                            },
                                        ],
                                        [
                                            'attribute' => 'gallery',
                                            'headerOptions' => ['style' => 'min-width: 100px;'],
                                            'value' => function ($model) {
                                                $galleries = ArrayHelper::map(\app\modules\admin\models\Gallery::find()->asArray()->all(), 'id', 'title');

                                                return $galleries[$model->gallery_id];
                                            },
                                        ],
                                        [
                                            'attribute' => 'status',
                                            'value' => function ($model) {
                                                return Yii::$app->params['content_status'][$model->status];
                                            },
                                        ],
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
