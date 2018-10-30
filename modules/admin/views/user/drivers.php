<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::$app->mv->gt('Водители', [], false);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h2></h2>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools pull-right">
                    <?= Html::a(Yii::$app->mv->gt('{i} Добавить', ['i' => Html::tag('i', '', ['class' => 'fa fa-plus'])], false), ['create'], ['class' => 'btn btn-default btn-sm']); ?>
                    <?= Html::a(
                        Yii::$app->mv->gt('{i} Удалить выбранные', ['i' => Html::tag('i', '', ['class' => 'fa fa-fire'])], false),
                        [''],
                        [
                            'class' => 'btn btn-danger btn-sm',
                            'onclick' => "
                                    var keys = $('#grid').yiiGridView('getSelectedRows');
                                    if (keys!='') {
                                        if (confirm('" . Yii::$app->mv->gt('Are you sure you want to delete the selected items?', [], false) . "')) {
                                            $.ajax({
                                                type : 'POST',
                                                data: {keys : keys},
                                                success : function(data) {}
                                            });
                                        }
                                    }
                                    return false;
                                ",
                        ]
                    ); ?>
                </div>
            </div>
            <!-- /.box-header -->
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'id' => 'grid',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'role' => 'button',
                        'onclick' => "window.location = '" . \yii\helpers\Url::toRoute("/admin/user/view/" . $key) . "'"
                    ];
                },
                'layout' => "
                    <div class='box-body' style='display: block;'><div class='col-sm-12 right-text'>{summary}</div><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>
                ",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\CheckboxColumn'],
                    ['attribute' => 'id', 'headerOptions' => ['style' => 'width: 50px;']],
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('app', "Имя"),
                        'value' => function ($model) {
                            $check = $model->approved == 1 ? ' <i class="fa fa-check text-success"></i>' : '';
                            return $model->fullName . $check;
                        },
                        'format' => 'html'
                    ],
                    'phone',
                    'email:email',
                    'city_id' => [
                        'attribute' => 'city_id',
                        'value' => function ($model) {
                            return ($model->city) ? $model->city->title : Yii::t('app', "Не задано");
                        }
                    ],
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('app', "Рейтинг"),
                        'value' => function ($model) {
                            return $model->rating;
                        },
                        'filter' => false
                    ]
                ]
            ]); ?>
        </div>
    </section>
</div>
