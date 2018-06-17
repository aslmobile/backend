<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\SourceMessage */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Admin panel', [], false), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section>
        <div class="content-header">
            <?= Html::tag('h1', $this->title) ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        </div>
        <div class="content">

            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>

            <div class="row">
                <div class="col-lg-12">

                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'id',
                            'category',
                            'message:ntext',
                        ],
                    ]) ?>

                </div>
            </div>

        </div>
    </section>
</div>
