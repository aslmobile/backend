<?php
/**
 * Created by PhpStorm.
 * User: Graf
 * Date: 10.07.2017
 * Time: 12:23
 */

namespace app\components;


use app\models\CompanyInfo;
use app\modules\admin\models\Translations;
use app\modules\main\models\Settings;
use Yii;

class ConsoleController extends \yii\console\Controller
{
    public $coreSettings;
    public $gt = [];
    public $lang = '';
    public $link = '';

    public function init()
    {
        Yii::setAlias('@webroot', __DIR__ . '/../web');

        // lang
        $this->lang = $this->getOldLangAssoc(Yii::$app->language);
        
        $this->gt = $this->getGt();
        
        $this->coreSettings = self::getCoreSettings();

        parent::init();
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getCoreSettings()
    {
        return Settings::find()
            ->where('id = 1')
            ->multilingual()
            ->one();
    }

    /**
     * @return array|mixed
     */
    public function getGt()
    {
        $cache = Yii::$app->cache;
        $data = $cache->get('gt_' . Yii::$app->language);

        if ($data === false) {
            if (Yii::$app->language == Yii::$app->params['sourceLanguage']) {
                $model = Translations::find()->all();
            } else {
                $model = Translations::find()->multilingual()->all();
            }
            $data = array();
            foreach ($model as $m) {
                #$data[$m['key']] = array('id' => $m['id'], 'val' => $m['val']);
                $data[$m->trans_key] = array('id' => $m->id, 'val' => $m->val);
            }
            $cache->set('gt_' . Yii::$app->language, $data);
        }
        return $data;
    }

    public static function getOldLangAssoc($lang = null)
    {
        $languages = ['en-US' => 'en', 'ru-RU' => 'ru', 'uk-UA' => 'uk'];
        return isset($languages[$lang]) ? $languages[$lang] : 'en';
    }

    public static function getNewLangAssoc($lang = null)
    {
        $languages = ['en' => 'en-US', 'ru' => 'ru-RU', 'uk' => 'uk-UA'];
        return isset($languages[$lang]) ? $languages[$lang] : 'en';
    }
}