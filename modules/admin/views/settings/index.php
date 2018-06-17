<?php
use yii\widgets\Breadcrumbs;
use zxbodya\yii2\elfinder\ElFinderWidget;
use app\components\widgets\Alert;

$this->title = Yii::$app->mv->gt('Configuration',[],false);
$this->params['breadcrumbs'][] = $this->title;

?>




<div class="content-wrapper">
	<section class="content-header">
        <h1>
            <?= $this->title; ?>
        </h1>
        <?= Breadcrumbs::widget([
            'tag' => 'ol',
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>

    <!-- Main content -->
    <section class="content">
        <?= Alert::widget() ?>
        <div class="row">
            <div class="col-lg-12">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
		</div>
    </section>
</div>