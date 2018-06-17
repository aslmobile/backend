<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\widgets\SocialAuth;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::$app->mv->gt('Password reset',[],false);
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->controller->bodyClass[] = 'hold-transition login-page page1200';
?>
<div class="login-box vac">
    <div class="login-logo">
        <?=Yii::$app->mv->gt('Password reset',[],true);?>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body rel">

        <form class="ajax" method="post" name="recover">
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="<?=Yii::$app->mv->gt('password',[],false);?>" name="ResetPasswordForm[password]">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?=Yii::$app->mv->gt('Reset');?></button>
                </div>
            </div>
        </form>

    </div>
</div>