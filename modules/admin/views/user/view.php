<?php
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = (strlen($model->fullName) > 3) ? $model->fullName : $model->email;

$breadcrumbs_label = Yii::t('app', "Пользователи");
$breadcrumbs_url = ['index'];
$view = 'view-user';

if ($model->type == $model::TYPE_DRIVER)
{
    $breadcrumbs_label = Yii::t('app', "Водители");
    $breadcrumbs_url = ['drivers'];
    $view = 'view-driver';
}
elseif ($model->type == $model::TYPE_PASSENGER)
{
    $breadcrumbs_label = Yii::t('app', "Пассажиры");
    $breadcrumbs_url = ['passengers'];
    $view = 'view-passenger';
}

$this->params['breadcrumbs'][] = ['label' => $breadcrumbs_label, 'url' => $breadcrumbs_url];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>&nbsp;</h1>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <?= $this->render($view, ['model' => $model]); ?>
    </section>
</div>
