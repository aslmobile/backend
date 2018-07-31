<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>
<div class="row">
    <div class="col-12 col-sm-8 col-md-6">
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
                    <a href="mailto:<?= $model->email ?>" style="color: white;"><?= $model->email ?></a><br />
                    <a href="tel:+<?= $model->phone ?>" style="color: white;">+<?= $model->phone ?></a>
                </h5>
            </div>
            <div class="box-body">
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
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'city_id' => [
                            'attribute' => 'city_id',
                            'value' => function ($model) {
                                return ($model->city) ? $model->city->title : Yii::t('app', "Не задано");
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'value' => key_exists($model->status, $model->statuses)? $model->statuses[$model->status] : null,
                        ],
                    ],
                ]) ?>
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
    <div class="col-12 col-sm-4 col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', "Водительское удостовирение"); ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <?php $license = \app\models\DriverLicence::findOne(['user_id' => $model->id]); ?>
                <?php if ($license) : ?>
                    <img class="img-responsive" src="<?= $license->image; ?>" />
                    <hr />
                    <img class="img-responsive" src="<?= $license->image2; ?>" />
                <?php else : ?>
                    <p class="text-center text-info"><?= Yii::$app->mv->gt("Водительское удостовирение не загружено", [], false); ?></p>
                <?php endif; ?>
            </div>
            <!-- /.box-body -->
        </div>
    </div>

    <?php $vehicles = \app\modules\admin\models\Vehicles::find()->where(['user_id' => $model->id])->all(); ?>
    <?php if ($vehicles && count($vehicles) > 0) : foreach ($vehicles as $vehicle) : ?>
        <div class="col-12 col-sm-8 col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $vehicle->vehicleName; ?></h3>
                </div>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $vehicle,
                        'attributes' => [
                            'license_plate',
                            'vehicle_type_id' => [
                                'attribute' => 'vehicle_type_id',
                                'value' => function ($model) {
                                    return $model->type->title;
                                }
                            ],
                            'vehicle_brand_id' => [
                                'attribute' => 'vehicle_brand_id',
                                'value' => function ($model) {
                                    return $model->brand->title;
                                }
                            ],
                            'seats'
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>