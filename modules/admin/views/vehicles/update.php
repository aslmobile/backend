<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\VehicleType | app\modules\admin\models\VehicleModel | app\modules\admin\models\VehicleBrand */

$this->title = Yii::$app->mv->gt('<span>{plate}</span>: {brand} {model} <small>Редактирование</small>', [
    'brand' => $model->brand->title,
    'model' => $model->model->title,
    'plate' => $model->license_plate
], false);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Автомобили',[],false), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->license_plate;
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
                <?= $this->render('form/' . $model->sc, ['model' => $model]); ?>
            </div>
        </div>
    </section>
</div>