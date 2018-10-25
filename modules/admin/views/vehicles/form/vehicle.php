<?php

use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Vehicles */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">

                <?= $form->field($model, 'status')->dropDownList($model->statusList) ?>
                <?= $form->field($model, 'main')->dropDownList(Yii::$app->params['main_vehicle_yes_no']) ?>
                <?= $form->field($model, 'seats')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'license_plate')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'generate_code')->checkbox() ?>

                <?php if (!empty($model->code)) { ?>
                    <?= \yii\helpers\Html::a(
                        \yii\helpers\Html::img(\yii\helpers\Url::to($model->code), ['class' => 'img-bordered', 'style' => 'max-width:500px;']),
                        \yii\helpers\Url::to($model->code),
                        [
                            'target' => '_blank',
                            'download' => "code"
                        ]
                    ); ?>
                <?php } ?>

            </div>
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'created_by',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Тип автомобиля")
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => \yii\helpers\Url::toRoute(['/admin/user/select-users']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("' . \yii\helpers\Url::toRoute(['/admin/user/select-users']) . '", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>

                <?= $form->field($model, 'vehicle_type_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'created_by',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Тип автомобиля")
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => \yii\helpers\Url::toRoute(['/admin/vehicles/select-types']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("' . \yii\helpers\Url::toRoute(['/admin/vehicles/select-types']) . '", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>

                <?= $form->field($model, 'vehicle_brand_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'created_by',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Тип автомобиля")
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => \yii\helpers\Url::toRoute(['/admin/vehicles/select-brands']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("' . \yii\helpers\Url::toRoute(['/admin/vehicles/select-brands']) . '", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>

                <?= $form->field($model, 'vehicle_model_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'created_by',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Тип автомобиля")
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => \yii\helpers\Url::toRoute(['/admin/vehicles/select-models']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("' . \yii\helpers\Url::toRoute(['/admin/vehicles/select-models']) . '", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>
            </div>
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-4 col-md-6">
        <?php if (!empty ($model->image) && intval($model->image) > 0) : ?>
            <div class="box">
                <div class="box-header with-border">
                    <?= Yii::t('app', "Фото автомобиля"); ?>
                </div>
                <div class="box-body text-center">
                    <?php $image = \app\modules\api\models\UploadFiles::findOne($model->image); ?>
                    <?php if ($image) : ?><img class="img-responsive img-bordered" src="<?= $image->file; ?>" />
                    <?php else : ?><p class="text-center text-info"><?= Yii::t('app', "Фото не загружено"); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-12 col-sm-4 col-md-6">
        <?php if (!empty ($model->insurance) && intval($model->insurance) > 0) : ?>
            <div class="box">
                <div class="box-header with-border">
                    <?= Yii::t('app', "Фото страховки"); ?>
                </div>
                <div class="box-body text-center">
                    <?php $image = \app\modules\api\models\UploadFiles::findOne($model->insurance); ?>
                    <?php if ($image) : ?><img class="img-responsive img-bordered" src="<?= $image->file; ?>" />
                    <?php else : ?><p
                            class="text-center text-info"><?= Yii::t('app', "Фото документа не загружено"); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-2 col-sm-4 col-md-12">
        <?php if (!empty ($model->registration) && intval($model->registration) > 0 || !empty ($model->registration2) && intval($model->registration2) > 0) : ?>
            <div class="box">
                <div class="box-header with-border">
                    <?= Yii::t('app', "Фото тех. паспорта"); ?>
                </div>
                <div class="box-body">
                    <div class="row">
                        <?php if (!empty ($model->registration) && intval($model->registration) > 0) : ?>
                            <div class="col-12 col-md-6">
                                <?php $image = \app\modules\api\models\UploadFiles::findOne($model->registration); ?>
                                <?php if ($image) : ?><img class="img-responsive img-bordered"
                                                           src="<?= $image->file; ?>" />
                                <?php else : ?><p
                                        class="text-center text-info"><?= Yii::t('app', "Фото документа не загружено"); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty ($model->registration2) && intval($model->registration2) > 0) : ?>
                            <div class="col-12 col-md-6">
                                <?php $image = \app\modules\api\models\UploadFiles::findOne($model->registration2); ?>
                                <?php if ($image) : ?><img class="img-responsive img-bordered"
                                                           src="<?= $image->file; ?>" />
                                <?php else : ?><p
                                        class="text-center text-info"><?= Yii::t('app', "Фото документа не загружено"); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
