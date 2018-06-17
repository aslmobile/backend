<?php
/**
 * @var \yii\db\ActiveRecord $model
 */

echo \yii\bootstrap\Html::a(Yii::$app->mv->gt('Редактировать', [], false), [
    'update',
    'id' => $model->id
], ['class' => 'btn btn-primary', 'style' => 'margin-right: 15px']);

echo \yii\bootstrap\Html::a(Yii::$app->mv->gt('Удалить', [], false), [
    'delete',
    'id' => $model->id
], [
    'class' => 'btn btn-danger',
    'data' => [
        'confirm' => Yii::$app->mv->gt('Вы уверены, что хотите удалить?', [], false),
        'method' => 'post',
    ],
]);