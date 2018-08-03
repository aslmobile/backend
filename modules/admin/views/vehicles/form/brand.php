<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use alexantr\elfinder\InputFile;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\VehicleType | app\modules\admin\models\VehicleModel | app\modules\admin\models\VehicleBrand */
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
                <?= $form->field($model, 'max_seats')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'image', ['template' => "{label}\n{input}"])->widget(InputFile::className(), [
                    'buttonText' => Yii::$app->mv->gt('Выбрать', [], false),
                    'options' => [
                        'language' => 'ru',
                        'class' => 'form-control',
                        'onchange' => <<<JS
                                    var changed = $(this);
                                    var val = $(changed).val();
                                    setTimeout(function() {
                                    if($(".elfinder-input-preview").length){
                                       $(".elfinder-input-preview").html($("<img/>", {src : val, width: 200, height: 'auto'}));
                                    }else{
                                        $(changed).parent().after('<div class="help-block elfinder-input-preview"></div');
                                        $(".elfinder-input-preview").html($("<img/>", {src : val, width: 200, height: 'auto'}));
                                    }
                                    }, 500);
JS
                    ],
                    'clientRoute' => 'el-finder/input',
                    'filter' => ['image'],
                    'preview' => function ($value) {
                        return yii\helpers\Html::img($value, ['width' => 200, 'id' => 'elfinder_preview']);
                    },
                ]); ?>

                <?= $form->field($model, 'vehicle_type_id')->widget(Select2::classname(), [
                    'model' => [],
                    'theme' => Select2::THEME_DEFAULT,
                    'attribute' => 'created_by',
                    'hideSearch' => true,
                    'options' => [
                        'placeholder' => Yii::t('app', "Тип автомобиля"),
                        'multiple' => true
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
            </div>
            <div class="box-footer clearfix text-right">
                <?= \app\components\widgets\FormButtons::widget(['model' => $model]) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
