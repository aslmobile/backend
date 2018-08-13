<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use app\components\widgets\Alert;

/* @var $this yii\web\View */
/* @var $model app\models\Taxi */


$this->title = Yii::$app->mv->gt('Редактирование заказа',[],false);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Заказы такси',[],false), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Заказ №{id}',['id' => $model->id],false), 'url' => ['view', 'id' => $model->id]];
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
