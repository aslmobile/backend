<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Route */

$this->title = Yii::$app->mv->gt('Добавление чекпоинта', [], false);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Чекпоинты', [], false), 'url' => ['checkpoints']];
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
        <div class="row">
            <div class="col-lg-12">
                <?= $this->render('_checkpoint_form', ['model' => $model]); ?>
            </div>
        </div>
    </section>
</div>