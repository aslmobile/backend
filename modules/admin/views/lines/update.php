<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use app\components\widgets\Alert;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Line */

$this->title = Yii::$app->mv->gt('Редиктирование маршрута', [], false);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Маршруты', [], false), 'url' => ['routes']];
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
        <div class="container-fluid"><?= Alert::widget(); ?></div>
        <div class="row">
            <div class="col-lg-12">
                <?= $this->render('_form', ['model' => $model]); ?>
            </div>
        </div>
    </section>
</div>