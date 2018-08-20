<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\widgets\Alert;

/* @var $this yii\web\View */

$this->title = Yii::$app->mv->gt('Водитель', [], false);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->mv->gt('Боты', [], false), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <section class="content-header">
        <h2><?= Html::encode($this->title) ?></h2>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
        <?= Alert::widget() ?>
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'form']]); ?>
                <div class="box box-widget">
                    <div class="box-header with-border bg-aqua">
                        <h3 class="box-title"><?= Yii::t('app', "Создание поездки"); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12"><?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?></div>
                            <div class="col-sm-12 col-md-6">
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
                            </div>
                            <div class="col-sm-12 col-md-6">
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
                                            'data' => new JsExpression('function(params) { return {q:params.term, v:$("#bots-driver_id").val()}; }')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(user) { return user.text; }'),
                                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                                        'initSelection' => new JsExpression('function(element, callback) { var id = $(element).val();if(id !== "") {$.ajax("'.\yii\helpers\Url::toRoute(['/admin/vehicles/select-vehicles']).'", {data: {id: id},dataType: "json"}).done(function(data) {callback(data.results);});}}'),
                                    ],
                                ]); ?>
                            </div>
                            <div class="col-sm-12">
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
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <?= $form->field($model, 'start_point_id')->widget(Select2::classname(), [
                                    'model' => [],
                                    'theme' => Select2::THEME_DEFAULT,
                                    'attribute' => 'start_point_id',
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
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <?= $form->field($model, 'end_point_id')->widget(Select2::classname(), [
                                    'model' => [],
                                    'theme' => Select2::THEME_DEFAULT,
                                    'attribute' => 'end_point_id',
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
                            </div>
                            <div class="col-sm-12">
                                <?= $form->field($model, 'status')->dropDownList(\app\models\Line::getStatusList()); ?>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-center">
                        <?php echo \yii\helpers\Html::submitButton(
                            Yii::$app->mv->gt('Создать', [], 0),
                            ['class' => 'btn btn-success']
                        ); ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </section>
</div>
