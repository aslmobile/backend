<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;


$this->title = Yii::$app->mv->gt("Языки",[],0);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt("Панель управления",[],0), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <?= Html::tag('h1', $this->title) ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </section>
</div>
