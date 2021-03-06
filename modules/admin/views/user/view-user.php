<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="row">
    <div class="col-sm-4">
        <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-aqua-active">
                <?php if(!empty($model->image)): ?>
                    <div class="widget-user-image">
                        <?= Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $model->image, '128x128', ['class' => 'img-circle']) ?>
                    </div>
                <?php endif; ?>
                <!-- /.widget-user-image -->
                <h3 class="widget-user-username"><?= $model->fullName ?></h3>
                <h5 class="widget-user-desc"><?= $model->email ?></h5>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">About</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <strong><i class="fa fa-file-text-o margin-r-5"></i>Информация о редактирование</strong>
                <hr>
                <?php if(!empty($model->created_at)):?>
                    <p><?= Yii::$app->mv->gt('Дата регистрации: {time}',['time'=>Yii::$app->formatter->asDatetime($model->created_at)],false)?></p>
                    <hr>
                <?php endif;?>
                <?php if(!empty($model->updated_at)):?>
                    <p><?= Yii::$app->mv->gt('Последнее обновление: {time}',['time'=>Yii::$app->formatter->asDatetime($model->updated_at)],false)?></p>
                    <hr>
                <?php endif;?>
            </div>
            <!-- /.box-body -->
        </div>
    </div>

    <div class="col-sm-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3></h3>
                <div class="box-tools pull-right">
                    <?= Html::a('Редактировать', [
                        'update',
                        'id' => $model->id
                    ], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Удалить', [
                        'delete',
                        'id' => $model->id
                    ], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Вы действительно хотите удалить пользователя?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
                                'first_name',
                                'second_name',
                                'email:email',
                                [
                                    'attribute' => 'status',
                                    'value' => key_exists($model->status, $model->statuses)? $model->statuses[$model->status] : null,
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>