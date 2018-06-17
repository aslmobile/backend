<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use app\components\widgets\Alert;


$this->title = Yii::$app->mv->gt("Edit",[],0) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt("Admin panel",[],0), 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt("Languages",[],0), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="content-wrapper">
    <section class="content-header">
        <?= Html::tag('h1', $this->title)?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
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
