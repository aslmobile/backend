<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\modules\user\models\SignupForm */



$this->title = Yii::$app->mv->gt('Sign up',[],false);
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->controller->bodyClass[] = 'hold-transition register-page page1200';



?>
<div class="register-box vac">
    <div class="register-logo">
        <?=Yii::$app->mv->gt('Sign up',[],false);?>
    </div>

    <div class="register-box-body rel">


        <form class="ajax" method="post" name="signup">

                <p class="login-box-msg"><?=Yii::$app->mv->gt('Register a new membership',[],true);?></p>



                <div class="row">
                    <div class="form-group has-feedback col-sm-6">
                        <input type="text" name="SignupForm[first_name]" class="form-control" placeholder="<?=Yii::$app->mv->gt('First name',[],false);?>">
                        <span class="glyphicon glyphicon-user form-control-feedback mr15"></span>
                    </div>
                    <div class="form-group has-feedback col-sm-6">
                        <input type="text" name="SignupForm[last_name]" class="form-control" placeholder="<?=Yii::$app->mv->gt('Last name',[],false);?>">
                        <span class="glyphicon glyphicon-user form-control-feedback mr15"></span>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <input type="email" name="SignupForm[email]" class="form-control" placeholder="<?=Yii::$app->mv->gt('Email',[],false);?>">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="text" name="SignupForm[phone]" class="form-control phone" placeholder="<?=Yii::$app->mv->gt('Phone',[],false);?>">
                    <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="SignupForm[password]" class="form-control" placeholder="<?=Yii::$app->mv->gt('Password',[],false);?>">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="SignupForm[repeat_password]" class="form-control" placeholder="<?=Yii::$app->mv->gt('Retype password',[],false);?>">
                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="SignupForm[rules]"> <?=Yii::$app->mv->gt('I agree with {a}terms{/a}', ['a' => '<a target="_blank" href="' . Url::toRoute('terms') . '">', '/a' => '</a>']);?>
                                <div class="input_error" onclick="hideError(this);" get="err_rules"></div>
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat"><?=Yii::$app->mv->gt('Sign up',[],false);?></button>
                    </div>
                    <!-- /.col -->
                </div>
                <?if(false){?><div class="social-auth-links text-center">
                    <p>- OR -</p>
                    <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using
                        Facebook</a>
                    <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using
                        Google+</a>
                </div>
                <?}?>

        </form>

        <div class="text-center pt14">
            <a href="<?=Url::toRoute('/login');?>" class="text-center"><?=Yii::$app->mv->gt('I already have a membership', [], false);?></a>
        </div>


    </div>
    <!-- /.form-box -->
</div>