<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\widgets\Alert;

/* @var $this yii\web\View */

$this->title = Yii::$app->mv->gt('Транзакции', [], false);
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
                        <h3 class="box-title"><?= Yii::t('app', "Создание транзакции"); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12"><?= $form->errorSummary($model, ['class' => 'alert-danger alert fade in']); ?></div>
                            <div class="col-sm-12 col-md-6">
                                <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
                                    'model' => [],
                                    'theme' => Select2::THEME_DEFAULT,
                                    'attribute' => 'user_id',
                                    'hideSearch' => true,
                                    'options' => [
                                        'placeholder' => Yii::t('app', "Водитель")
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
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <?= $form->field($model, 'transaction_amount')->textInput(['type' => 'number']); ?>
                            </div>
                            <div class="col-sm-12">
                                <?= $form->field($model, 'transaction_gateway')->dropDownList(\app\models\Transactions::getGatewayServices()); ?>
                                <?= $form->field($model, 'transaction_status')->dropDownList(\app\models\Transactions::getStatusList()); ?>
                                <?= $form->field($model, 'transaction_type')->dropDownList(\app\models\Transactions::getTypeList()); ?>
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
