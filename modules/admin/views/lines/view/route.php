<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Route */

$this->title = (strlen($model->title) > 3) ? $model->title : $model->id;
$this->params['breadcrumbs'][] = [
    'label' => 'Маршруты',
    'url' => ['routes']
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
            <div class="col-sm-6 col-sm-offset-3">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= Yii::$app->mv->gt('Инфомрация', [], false); ?></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                [
                                    'attribute' => 'status',
                                    'value' => key_exists($model->status, $model->statusList)? $model->statusList[$model->status] : null,
                                ],
                                'title',
                                'base_tariff'
                            ],
                        ]) ?>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
</div>
