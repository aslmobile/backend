<?php

use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->mv->gt('File manager', [], false);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?= $this->title ?></h1>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">

                <?= alexantr\elfinder\ElFinder::widget([
                    'connectorRoute' => ['el-finder/connector'],
                    'settings' => [
                        'height' => 640,
                    ],
                    'buttonNoConflict' => true,
                ]) ?>

            </div>
        </div>
    </section>
</div>
