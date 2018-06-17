<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\widgets\SocialAuth;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::$app->mv->gt('Account recovery',[],false);
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->controller->bodyClass[] = 'hold-transition login-page page1200';
?>
<div class="login-box vac">
    <div class="login-logo">
        <?=Yii::$app->mv->gt('Account recovery',[],true);?>
    </div>
    <div class="login-box-body rel">

        <form class="ajax" method="post" name="recover">
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="<?=Yii::$app->mv->gt('Email',[],false);?>" name="PasswordResetRequestForm[email]">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?=Yii::$app->mv->gt('Recover');?></button>
                </div>
            </div>
        </form>

    </div>
</div>