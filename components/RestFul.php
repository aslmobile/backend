<?php namespace app\components;

use Yii;
use yii\web\Response;
use yii\web\HttpException;

use app\models\Settings;

class RestFul extends \yii\rest\ActiveController
{
    protected $auth_token;

    public $lang = 'en';
    public $coreSettings;

    public function init()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        parent::init();

        $this->lang = $this->getOldLangAssoc(Yii::$app->language);
        $this->coreSettings = self::getCoreSettings();

        $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) $this->auth_token = $matches[1];
        else throw new HttpException(403, 'Authorization required!');
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getCoreSettings()
    {
        return Settings::find()->where('id = 1')->multilingual()->one();
    }

    public static function getOldLangAssoc($lang)
    {
        $languages = Yii::$app->controller->oldLangAssoc;
        return isset($languages[$lang]) ? $languages[$lang] : 'en';
    }

    public static function getNewLangAssoc($lang)
    {
        $languages = Yii::$app->controller->newLangAssoc;
        return isset($languages[$lang])? $languages[$lang] : 'en';
    }
}