<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

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
                <?= $form->field($model, 'main')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'seats')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'license_plate')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>
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
                    'theme' => Select2::THEME_KRAJEE,
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
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/user/select-users']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>

                <?= $form->field($model, 'vehicle_type_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_KRAJEE,
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
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/vehicles/select-types']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>

                <?= $form->field($model, 'vehicle_brand_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_KRAJEE,
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
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/vehicles/select-brands']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>

                <?= $form->field($model, 'vehicle_model_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_KRAJEE,
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
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/vehicles/select-models']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>
            </div>
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
