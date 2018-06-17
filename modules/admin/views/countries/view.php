<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\Countries */

$this->title = $model->title_en;
$this->params['breadcrumbs'][] = [
    'label' => 'Countries',
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
                                        [
                                            'attribute' => 'flag',
                                            'value' => $model->flag,
                                            'format' => ['image', ['width' => '100']],
                                        ],
                                        'title_en',
                                        'code_alpha2',
                                        'code_alpha3',
                                        //'dc',
                                        //'title_he',
                                        //'title_zh',
                                        //'title_cz',
                                        //'title_lv',
                                        //'title_lt',
                                        //'title_ja',
                                        //'title_po',
                                        //'title_it',
                                        //'title_fr',
                                        //'title_de',
                                        //'title_pt',
                                        //'title_es',
                                        //'title_be',
                                        //'title_ua',
                                        //'title_ru',
                                        'code_iso',
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
