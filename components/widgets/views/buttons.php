<?php
/**
 * @var \yii\db\ActiveRecord $model
 * @var string $backUrl
 */

echo \yii\helpers\Html::a(
    Yii::$app->mv->gt('Отменить', [], 0),
    $backUrl,
    ['class' => 'btn btn-danger', 'style' => 'margin-right: 15px', 'onclick' => 'window.history.go(-1); return false;']
);

echo \yii\helpers\Html::submitButton((
    $model->isNewRecord ?
        Yii::$app->mv->gt('Добавить', ['i' => \yii\helpers\Html::tag('i', '', ['class' => 'fa fa-save'])], 0)
        : Yii::$app->mv->gt('Сохранить', ['i' => \yii\helpers\Html::tag('i', '', ['class' => 'fa fa-save'])], 0)),
    ['class' => 'btn btn-success']
);