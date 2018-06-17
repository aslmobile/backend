<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\widgets\SocialAuth;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \models\LoginForm */

$this->title = Yii::$app->mv->gt('Sign in',[],false);
?>

<div class="login-box">
    <div class="login-logo text-center">
        <?= Html::a(
                Yii::$app->imageCache->img(Yii::getAlias('@webroot') . Yii::$app->controller->coreSettings->logo, 'x200', [
                    'alt' => Yii::$app->controller->coreSettings->name
                ])
                ,'/');?>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg"><?= Html::encode($this->title) ?></p>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'email', [
                    'template' => '{input}<span class="glyphicon glyphicon-envelope form-control-feedback"></span>{error}{hint}',
                    'options' => [
                        'class' => 'form-group has-feedback'
                    ]
                ])
                ->textInput(['placeholder'=>'Email']); ?>
            <?= $form->field($model, 'password', [
                    'template' => '{input}<span class="glyphicon glyphicon-lock form-control-feedback"></span>{error}{hint}',
                    'options' => [
                        'class' => 'form-group has-feedback'
                    ]
                ])
                ->passwordInput(['placeholder'=>'Password']) ?>

<!--            <div class="row">-->
                    <?= Html::submitButton(Yii::t('app', 'Sign In'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
<!--            </div>-->
        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.login-box-body -->
</div>


