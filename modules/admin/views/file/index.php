<?php
use yii\widgets\Breadcrumbs;
use alexantr\elfinder\ElFinder;

$this->title = Yii::$app->mv->gt('Менеджер файлов',[],false);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><?= $this->title?></h1>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
	</section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">

                <?= ElFinder::widget(
                    ['connectorRoute' => 'el-finder/connector',]
                ) ?>

            </div>
        </div>
    </section>
</div>