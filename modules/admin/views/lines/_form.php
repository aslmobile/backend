<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Line */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">
                <?= $form->field($model, 'status')->dropDownList($model->statusList) ?>
                <?= $form->field($model, 'driver_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'driver_id',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Водитель")
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => \yii\helpers\Url::toRoute(['/admin/user/select-drivers']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/user/select-drivers']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>
                <?= $form->field($model, 'vehicle_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'vehicle_id',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Автомобиль")
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => \yii\helpers\Url::toRoute(['/admin/vehicles/select-vehicles']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term, v:$("#line-driver_id").val()}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/vehicles/select-vehicles']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>
                <?= $form->field($model, 'tariff')->textInput(['maxlength' => true, 'type' => "numeric", 'step' => 0.1]) ?>
                <?= $form->field($model, 'route_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'route_id',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Маршрут")
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => \yii\helpers\Url::toRoute(['/admin/lines/select-route']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/lines/select-route']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>
                <?= $form->field($model, 'startpoint')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'startpoint',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Начальная точка")
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => \yii\helpers\Url::toRoute(['/admin/lines/select-startpoints']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/lines/select-startpoints']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>
                <?= $form->field($model, 'endpoint')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'endpoint',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Конечная точка")
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'ajax' => [
                            'url' => \yii\helpers\Url::toRoute(['/admin/lines/select-endpoints']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/lines/select-endpoints']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                    ],
                ]); ?>

                <?= $form->field($model, 'seats')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'freeseats')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'endtime')->textInput(['maxlength' => true, 'type' => 'datetime-local']) ?>
                <?= $form->field($model, 'starttime')->textInput(['maxlength' => true, 'type' => 'datetime-local']) ?>

                <?= $form->field($model, 'cancel_reason')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
