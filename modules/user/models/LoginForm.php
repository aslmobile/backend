<?php

namespace app\modules\user\models;

use app\modules\user\models\User;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;
    private $user = false;

    /*
    0 - отсутствует
    1 - смс
    2 - google
    3 - email
     */


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }
	
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Эл. почта'),
            'password' => Yii::t('app', 'Пароль'),
        ];
    } 	

    /**
     * Validates the email and password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
 
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', Yii::$app->mv->gt('Пароль не верный',[],false));
            } elseif ($user && $user->status == User::STATUS_BLOCKED) {
                $this->addError('email', Yii::$app->mv->gt('Аккаунт заблокирован',[],false));
            } elseif ($user && $user->status == User::STATUS_PENDING) {
                $this->addError('email', Yii::$app->mv->gt('Аккаунт не подтвержден',[],false));
            }
        }
    }
 
    /**
     * Logs in a user using the provided email and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        } else {
            return false;
        }
    }
 
    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->email);
        }
 
        return $this->_user;
    }

    public function verifyGCode($attribute){
        if($this->$attribute){
            if( $curl = curl_init() ) {
                curl_setopt($curl, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, "secret=6Lf5miETAAAAAGPqtIsTlD1LHs-Qp00zXQE99prw&response=".$this->$attribute."&remoteip=".$_SERVER['REMOTE_ADDR']);
                $out = curl_exec($curl);
                if($out){
                    $out = json_decode($out);
                    if($out->success===true){
                        return true;
                    }
                }
                curl_close($curl);
            }

        }
        $this->addError($attribute, Yii::$app->mv->gt('Капча не может быть проверена в данный момент. Попробуйте позже.',[],false));
        return false;
    }

    public function verify2auth($attribute){
        if(!count($this->getErrors())){
            $user = $this->_user;
            $this->user = $user;
            if($user->auth2){

                $checkAuth2 = false;
                if($user->auth2_code){
                    $checkAuth2 = $user->auth2_code==$this->auth2;
                    if($user->auth2==self::AUTH2_GOOGLE){//google
                        require_once($_SERVER['DOCUMENT_ROOT'].'/components/GoogleAuth/lib/GoogleAuthenticator.php');
                        $ga=new \GoogleAuthenticator;
                        $code=$ga->getCode($user->ga_secret);
                        $checkAuth2 = $this->auth2 == $code;
                    }
                }

                if($user->auth2_code && $checkAuth2){
                    $user->auth2_code = '';
                    $user->save();
                    return true;
                }else{
                    if(time()>$user->auth2_last_sent_at+60*1){
                        $user->auth2_last_sent_at = time();
                        $user->auth2_code = mb_strtolower(Yii::$app->mv->generateRandomString(6));
                        $user->save();

                        $mess = Yii::$app->mv->gt('Auth code for {login}: {code}', ['login' => $_SERVER['HTTP_HOST'], 'code' => $user->auth2_code], false);

                        switch($user->auth2){
                            case self::AUTH2_EMAIL://email
                                Yii::$app->mv->mailTo($user->email, ['type' => 'auth2', 'rep' => ['login' => $user->email, 'code' => $user->auth2_code]]);
                                break;
                            case self::AUTH2_SMS://sms
                                //$file = file_get_contents('https://smsc.ru/sys/send.php?login=wexis&psw=NitYg44Nu8&phones='.($user->phone).'&mes='.($mess).'&charset=utf-8');
                                break;
                        }

                    }
                    if($this->auth2){
                        $this->addError('auth2', Yii::$app->mv->gt('Code is incorrect. Try again.'));
                    }else{
                        switch($user->auth2){
                            case self::AUTH2_SMS:
                                $this->addError('auth2', Yii::$app->mv->gt('SMS with code has been sent to your phone'));
                                break;
                            case self::AUTH2_GOOGLE:
                                $this->addError('auth2', Yii::$app->mv->gt('Enter code generated by your GA device'));
                                break;
                            case self::AUTH2_EMAIL:
                                $this->addError('auth2', Yii::$app->mv->gt('Verification code has been sent to your email address'));
                                break;
                        }

                    }
                }
            }
        }
        return true;
    }
}
