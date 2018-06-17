<?php
use app\components\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Gallery */

$this->title = Yii::$app->mv->gt('Edit Gallery {title}', ['title' => $model->title], false);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Gallery', [], 0), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::$app->mv->gt('Gallery edit', [], 0);
?>

<div class="content-wrapper">
    <section class="content-header">
        <?= Html::tag('h1', $this->title) ?>
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
