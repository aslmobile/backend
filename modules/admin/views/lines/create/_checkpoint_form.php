<?php

use kartik\select2\Select2;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Checkpoint */
/* @var $form yii\widgets\ActiveForm */


$children = [];
if (!$model->isNewRecord) {
    $children = $model->childrenR;
    if (!empty($children)) {$children = \yii\helpers\ArrayHelper::getColumn($children, 'id');}
}
$children_v = implode(",", $children);
$all_children = \app\models\Checkpoint::getAllChildren(!$model->isNewRecord ? ['route' => $model->route] : null);

$script = <<< JS
    function loadChildren(id) {
        $.ajax('/admin/lines/children?id='+id, {
            type: "POST",
            async: false,
            data: {route: id},
            beforeSend: function (xhr) { },
            error: function (xhr) { console.log(xhr); },
            success: function (response) {
                $('#checkpoint-children').html('');
                $.each(response, function(key, value) {
                    var newOption = new Option(value, key, false, false);
                    $('#checkpoint-children').append(newOption).trigger('change');
                });
                initSelect2();
            }
        });
    }
    function toggleChildren(value){
            switch (Number(value)){
            case 1:
                loadChildren($('#checkpoint-route').val());
                $('.field-checkpoint-children').removeClass('hidden');
            break;
            case 2:
                $('.field-checkpoint-children').addClass('hidden');
                $('#checkpoint-children').html('');
                $('#checkpoint-children').val(null);
            break;
            case 3:
                $('.field-checkpoint-children').addClass('hidden');
                $('#checkpoint-children').html('');
                $('#checkpoint-children').val(null);
            break;
        }
    }
    $('#checkpoint-route').change(function() {
        loadChildren($(this).val());
    });
    $('#checkpoint-type').change(function() {
        toggleChildren($(this).val());
    });
    toggleChildren($('#checkpoint-type').val());
JS;
$this->registerJs($script, \yii\web\View::POS_READY);


?>

<?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
<?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?>
<div class="row">
    <div class="col-md-6">
        <div class="box">
            <div class="box-body">

                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'status')->dropDownList($model->statusList) ?>
                <?= $form->field($model, 'type')->dropDownList($model->typesList) ?>

                <?= $form->field($model, 'children', ['template' => '{label}<br>{input}{error}{hint}'])->dropdownList(
                    $all_children,
                    [
                        'placeholder' => Yii::$app->mv->gt('Городские остановки', [], false),
                        'selvalue' => $children_v,
                        'vn' => count($children),
                        'class' => 'sel2 mpsel outst',
                        'multiple' => 'true'
                    ]
                ); ?>

                <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'route')->widget(Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map
                    (\app\modules\admin\models\Route::find()
                        ->where(['status' => \app\models\Route::STATUS_ACTIVE])->all(), 'id', 'title'),
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'route_id',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Маршрут")
                    ],
                    'pluginOptions' => ['allowClear' => true]
                ]); ?>

                <!--                --><? //= $form->field($model, 'country_id')->widget(Select2::classname(), [
                //                    'model' => [],
                //                    'theme' => Select2::THEME_DEFAULT,
                //                    'attribute' => 'country_id',
                //                    'hideSearch' => true,
                //                    'options' => [
                //                        'placeholder' => Yii::$app->mv->gt('Найти страну', [], false)
                //                    ],
                //                    'pluginOptions' => [
                //                        'allowClear' => true,
                //                        'minimumInputLength' => 1,
                //                        'ajax' => [
                //                            'url' => \yii\helpers\Url::toRoute(['/admin/default/select-countries']),
                //                            'dataType' => 'json',
                //                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                //                        ],
                //                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                //                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                //                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                //                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/default/select-countries']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                //                    ],
                //                ]); ?>
                <!---->
                <!--                --><? //= $form->field($model, 'region_id')->widget(Select2::classname(), [
                //                    'model' => [],
                //                    'theme' => Select2::THEME_DEFAULT,
                //                    'attribute' => 'region_id',
                //                    'hideSearch' => true,
                //                    'options' => [
                //                        'placeholder' => Yii::$app->mv->gt('Найти регион', [], false)
                //                    ],
                //                    'pluginOptions' => [
                //                        'allowClear' => true,
                //                        'minimumInputLength' => 1,
                //                        'ajax' => [
                //                            'url' => \yii\helpers\Url::toRoute(['/admin/default/select-regions']),
                //                            'dataType' => 'json',
                //                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                //                        ],
                //                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                //                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                //                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                //                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/default/select-regions']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                //                    ],
                //                ]); ?>
                <!---->
                <!--                --><? //= $form->field($model, 'city_id')->widget(Select2::classname(), [
                //                    'model' => [],
                //                    'theme' => Select2::THEME_DEFAULT,
                //                    'attribute' => 'city_id',
                //                    'hideSearch' => true,
                //                    'options' => [
                //                        'placeholder' => Yii::$app->mv->gt('Найти город', [], false)
                //                    ],
                //                    'pluginOptions' => [
                //                        'allowClear' => true,
                //                        'minimumInputLength' => 1,
                //                        'ajax' => [
                //                            'url' => \yii\helpers\Url::toRoute(['/admin/default/select-cities']),
                //                            'dataType' => 'json',
                //                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                //                        ],
                //                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                //                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                //                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                //                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/default/select-cities']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                //                    ],
                //                ]); ?>

                <?= $form->field($model, 'address')->widget(\kalyabin\maplocation\SelectMapLocationWidget::className(), [
                    'attributeLatitude' => 'latitude',
                    'attributeLongitude' => 'longitude',
                    'googleMapApiKey' => 'AIzaSyALfPPffcWHUHCDKccaIlBj5kLfQjIcD9w',
                    'draggable' => true,
                ])->label(Yii::t('app', "Местоположение")); ?>

            </div>
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
