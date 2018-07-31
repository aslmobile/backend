<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>
<div class="row">
    <div class="col-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
        <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-aqua-active">
                <?php if (!empty($model->image) && intval($model->image) > 0) : ?>
                    <?php $image = \app\modules\api\models\UploadFiles::findOne($model->image); ?>
                    <?php if ($image) : $image = $image->file; ?>
                        <div class="widget-user-image">
                            <?= Yii::$app->imageCache->img(Yii::getAlias('@webroot') . $image, '128x128', ['class' => 'img-circle']) ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- /.widget-user-image -->
                <h3 class="widget-user-username" style="font-weight: 500;"><?= $model->fullName ?></h3>
                <h5 class="widget-user-desc">
                    <a href="tel:+<?= $model->phone ?>" style="color: white;">+<?= $model->phone ?></a>
                </h5>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-sm-6 border-right">
                        <div class="description-block">
                            <h5 class="description-header text-uppercase"><?= Yii::t('app', "Рейтинг"); ?></h5>
                            <span class="description-text fa-2x"><?= $model->rating; ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="description-block">
                            <h5 class="description-header text-uppercase"><?= Yii::t('app', "Километры"); ?></h5>
                            <span class="description-text fa-2x"><?= $model->km; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-12 text-center">
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
            </div>
        </div>
    </div>
</div>