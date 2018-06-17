<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Feedback;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Feedback */
/* @var $form yii\widgets\ActiveForm */
$model = new Feedback();
?>
<div class="col-xl-8 col-xxl-9 contact-items">
    <p class="contact-item-title" data-aos="fade-down">Write us</p>
    <?php $form = ActiveForm::begin([
        'options' =>
            [
                'class' => 'form-card',
                'data-aos' => 'fade-down'
            ],
    ]); ?>
    <?= $form->field($model, 'name', [
        'inputOptions' =>
            [
                'id' => 'field-name',
                'class' => 'form-element-field',
                'placeholder' => ' ',
                'name' => 'name',
                'title' => ' '
            ],
        'labelOptions' =>
            [
                'class' => 'form-element-label'
            ],
        'options' => [
            'class' => 'form-element form-input'

        ],
        'template' => "{input}<div class='form-element-bar'></div>{label}\n<small class='form-element-hint'>{error}{hint}</small>",
    ])->input('input') ?>

    <?= $form->field($model, 'email', [
        'options' => [
            'class' => 'form-element form-input'
        ],
        'inputOptions' =>
            [
                'id' => 'field-email',
                'class' => 'form-element-field',
                'placeholder' => ' ',
                'name' => 'email',
                'title' => ' '
            ],
        'labelOptions' =>
            [
                'class' => 'form-element-label'
            ],
        'template' => "{input}<div class='form-element-bar'></div>{label}\n<small class='form-element-hint'>{error}{hint}</small>",
    ])->input('email') ?>

    <?= $form->field($model, 'message', [
        'options' => [
            'class' => 'form-element form-textarea'
        ],
        'inputOptions' =>
            [
                'id' => 'field-message',
                'class' => 'form-element-field',
                'placeholder' => ' ',
                'name' => 'message'
            ],
        'labelOptions' =>
            [
                'class' => 'form-element-label'
            ],
        'template' => "{input}<div class='form-element-bar'></div>{label}\n<small class='form-element-hint'>{error}{hint}</small>",
    ])->textarea() ?>

    <div class="form-element btn-element">
        <label class="for-btn" for="field-btn">Send request</label>
        <?= Html::submitButton(
            "<img src=\"" . Yii::$app->controller->getTune(40, false) . "\" alt=\"send\">",
            [
                'id' => 'field-btn',
                'class' => 'form-btn'
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$url = \yii\helpers\Url::toRoute(['/main/default/feedback']);
$js = <<<JS
    $('form').on('beforeSubmit', function(){
        var data = $(this).serialize();
        $.ajax({
           url: '$url',
           type: 'POST',
           data: data,
           success: function(res){
               $.alert({
                 title: 'Thank you!',
                 content: 'Your message has been sent!',
                 theme: 'supervan',
                });
               jQuery('form')[0].reset();
           },
           error: function() {
             
           }
        });
    return false;
    });
JS;
$this->registerJs($js);
?>