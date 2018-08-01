<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
?>
<div class="row">
    <div class="col-12 col-md-6">
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
    <div class="col-12 col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', "Водительское удостовирение"); ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <?php $license = \app\models\DriverLicence::findOne(['user_id' => $model->id]); ?>
                <?php if ($license) : ?>
                <div class="row">
                    <div class="col-12 col-md-6 border-right"><img class="img-responsive" src="<?= $license->image; ?>" /></div>
                    <div class="col-12 col-md-6 border-left"><img class="img-responsive" src="<?= $license->image2; ?>" /></div>
                </div>
                <?php else : ?>
                    <p class="text-center text-info"><?= Yii::$app->mv->gt("Водительское удостовирение не загружено", [], false); ?></p>
                <?php endif; ?>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
<div class="row">
    <?php $vehicles = \app\modules\admin\models\Vehicles::find()->where(['user_id' => $model->id])->all(); ?>
    <?php if ($vehicles && count($vehicles) > 0) : foreach ($vehicles as $vehicle) : ?>
        <div class="col-12 col-sm-8 col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $vehicle->vehicleName; ?></h3>
                </div>
                <?php if (!empty ($vehicle->image) && intval($vehicle->image) > 0) : ?>
                    <?php $image = \app\modules\api\models\UploadFiles::findOne($vehicle->image); ?>
                    <?php if ($image) : ?>
                        <div class="box-body">
                            <img class="img-responsive img-bordered" src="<?= $image->file; ?>" />
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="box-body">
                    <?= DetailView::widget([
                        'model' => $vehicle,
                        'attributes' => [
                            'status' => [
                                'attribute' => 'status',
                                'value' => function ($data) {
                                    return key_exists($data->status, $data->statusList) ? $data->statusList[$data->status] : false;
                                }
                            ],
                            'vehicle_type_id' => [
                                'attribute' => 'vehicle_type_id',
                                'value' => function ($model) {
                                    return ($model->type) ? $model->type->title : $model->vehicle_type_id;
                                }
                            ],
                            'vehicle_brand_id' => [
                                'attribute' => 'vehicle_brand_id',
                                'value' => function ($model) {
                                    return ($model->brand) ? $model->brand->title : $model->vehicle_brand_id;
                                }
                            ],
                            'vehicle_model_id' => [
                                'attribute' => 'vehicle_model_id',
                                'value' => function ($model) {
                                    return ($model->model) ? $model->model->title : $model->vehicle_model_id;
                                }
                            ],
                            'license_plate',
                            'seats',
                            'rating'
                        ],
                    ]) ?>
                </div>

                <?php
                if (empty ($vehicle->insurance) || intval($vehicle->insurance) == 0) $file = false;
                else $file = \app\modules\api\models\UploadFiles::findOne($vehicle->insurance);

                if (empty ($vehicle->registration) || intval($vehicle->registration) == 0) $file_r1 = false;
                else $file_r1 = \app\modules\api\models\UploadFiles::findOne($vehicle->registration);

                if (empty ($vehicle->registration2) || intval($vehicle->registration2) == 0) $file_r2 = false;
                else $file_r2 = \app\modules\api\models\UploadFiles::findOne($vehicle->registration2);
                ?>

                <div class="box-footer">
                    <?php if ($file) : ?>
                        <button class="btn btn-primary" data-toggle="modal" data-target=".v<?= $vehicle->id; ?>-insurance-modal-front">
                            <?= Yii::t('app', "Страхование"); ?>
                        </button>
                    <?php else : ?>
                        <button class="btn btn-primary disabled">
                            <?= Yii::t('app', "Страхование"); ?>
                            <?= Yii::t('app', "(не загружен)"); ?>
                        </button>
                    <?php endif; ?>
                    <?php if ($file_r1 || $file_r2) : ?>
                        <button class="btn btn-primary" data-toggle="modal" data-target=".v<?= $vehicle->id; ?>-registration-modal-front">
                            <?= Yii::t('app', "Тех. паспорт"); ?>
                        </button>
                    <?php else : ?>
                        <button class="btn btn-primary disabled">
                            <?= Yii::t('app', "Тех. паспорт"); ?>
                            <?= Yii::t('app', "(не загружен)"); ?>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if ($file) : ?>
                <div class="modal fade v<?= $vehicle->id; ?>-insurance-modal-front" tabindex="-1" role="dialog" aria-labelledby="registration-photo">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <?php if ($file) : ?><img class="img-responsive img-bordered" src="<?= $file->file; ?>" /><hr />
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($file_r1 || $file_r2) : ?>
                <div class="modal fade v<?= $vehicle->id; ?>-registration-modal-front" tabindex="-1" role="dialog" aria-labelledby="registration-photo">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <?php if ($file_r1) : ?><img class="img-responsive img-bordered" src="<?= $file_r1->file; ?>" /><hr />
                            <?php endif; ?>

                            <?php if ($file_r2) : ?><img class="img-responsive img-bordered" src="<?= $file_r2->file; ?>" /><hr />
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>