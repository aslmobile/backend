<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Checkpoint */
/* @var $form yii\widgets\ActiveForm */
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
                <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'route')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'route',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::$app->mv->gt('Найти маршрут', [], false)
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

                <?= $form->field($model, 'latitude')->hiddenInput()->label(false); ?>
                <?= $form->field($model, 'longitude')->hiddenInput()->label(false); ?>

                <div id="map" class="map"
                     data-latitude="<?= !empty($model->latitude) ? $model->latitude : isset($model->city->lat) ? $model->city->lat : null; ?>"
                     data-longitude="<?= !empty($model->longitude) ? $model->longitude : isset($model->city->lng) ?$model->city->lng  : null; ?>">
                </div>
                <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCFETIaFfGBDmEdSqldObecM6K6lSzSrl4&libraries=places&callback=initMap&hl=en&language=en"></script>
            </div>
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
