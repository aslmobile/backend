<?php
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\web\JsExpression;

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

        <div class="box box-widget">
            <div class="box-header with-border bg-aqua">
                <h3 class="box-title"><?= Yii::t('app', "Транзакции"); ?></h3>
            </div>
            <?= GridView::widget([
                'dataProvider' => $model->transactionsDataProvider,
                'id' => 'grid',
                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [];
                },
                'layout' => "
                    <div class='box-body' style='display: block;'><div class='col-sm-12 right-text'>{summary}</div><div class='col-sm-12'>{items}</div></div>
                    <div class='box-footer' style='display: block;'>{pager}</div>",
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover dataTable',
                ],
                'filterModel' => $model->transactionsSearchModel,
                'columns' => [
                    [
                        'attribute' => 'gateway',
                        'content' => function ($data) {
                            $gateways = \app\models\Transactions::getGatewayServices();
                            return key_exists($data->gateway, $gateways) ? $gateways[$data->gateway] : false;
                        },
                        'filter' => \app\models\Transactions::getGatewayServices(),
                    ],
                    [
                        'attribute' => 'type',
                        'content' => function ($data) {
                            $types = \app\models\Transactions::getTypeListArrows();
                            return key_exists($data->type, $types) ? $types[$data->type] : false;
                        },
                        'filter' => \app\models\Transactions::getTypeList(),
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'status',
                        'content' => function ($data) {
                            $statuses = \app\models\Transactions::getStatusList();
                            return key_exists($data->status, $statuses) ? $statuses[$data->status] : false;
                        },
                        'filter' => \app\models\Transactions::getStatusList(),
                        'format' => 'html'
                    ],
                    'amount',
                    'created_at:datetime'
                ],
            ]); ?>
        </div>
    </div>
</div>