<?php namespace app\components;

use app\models\Menu;
use app\models\Settings;
use app\models\Tune;
use app\modules\admin\models\Lang;
use app\modules\main\models\Dynamic;
use app\modules\main\models\Metadata;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class Controller
 * @package app\components
 */

class Controller extends \yii\web\Controller
{
    /** @var Settings $coreSettings */
    public $coreSettings;

    public $gt = [];
    public $lang = '';
    public $link = '';
    public $tune = [];

    public $rand = '';
    public $uip = '';

    public $langs = [];
    public $oldLangAssoc = [];
    public $newLangAssoc = [];

    public $bodyClass = [];

    public function init()
    {

        //$start = microtime(true);
        $u = Yii::$app->user->identity;

        // core settings
        $this->coreSettings = self::getCoreSettings();

        $this->getLangs();

        $uip = $_SERVER['REMOTE_ADDR'];
        if ($u && array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
        {
            $str = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $u->uip = $str[count($str) - 1];
        }

        Yii::$app->params['sourceLanguage'] = Lang::find()->where(['default' => 1])->one()->local;

        // preset
        $this->tune = $this->getTunes();

        // lang
        $this->lang = $this->oldLangAssoc[Yii::$app->language];

        // link
        $this->link = $this->getLink();

        if (!empty($this->coreSettings->maint) && !in_array($uip, Yii::$app->params['allowedIps'])) {
            $this->layout = '/maintenance';
        }

        $this->uip = $uip;

        parent::init();
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getCoreSettings()
    {
        return Settings::find()->where('id = 1')->one();
    }

    public function runAction($id, $params = [])
    {
        //Main meta tags
        $this->view->title = $this->coreSettings->title;
        $this->view->registerMetaTag(['name' => 'description', 'content' => $this->coreSettings->description]);
        $this->view->registerMetaTag(['name' => 'keywords', 'content' => $this->coreSettings->keywords]);

        $this->view->registerMetaTag(['name' => 'msapplication-TileColor', 'content' => "#ffffff"]);
        $this->view->registerMetaTag(['name' => 'msapplication-TileImage', 'content' => "/ms-icon-144x144.png"]);
        $this->view->registerMetaTag(['name' => 'theme-colo', 'content' => "#ffffff"]);
        $this->view->registerMetaTag(['name' => 'author', 'content' => "v-jet group"]);

        // If Maintenance Mode
        if ($this->isMaintenanceMode($id)) {
            $this->layout = '/maintenance';
            $action = $this->createAction($id);
            $this->beforeAction($action);

            return $this->render('@app/views/layouts/maintenance', []);
        }

        return parent::runAction($id, $params);
    }

    public function isMaintenanceMode($controller_action)
    {
        if (!empty($this->coreSettings->maint))
        {
            $controller = Yii::$app->controller;
            $module = $controller->module->id;

            if (isset($controller->action)) $controller_action = $controller->action->id;
            if (in_array($_SERVER['REMOTE_ADDR'], Yii::$app->params['allowedIps'])) return false;
            elseif (!Yii::$app->user->isGuest && Yii::$app->user->identity->hasPermission(['admin', 'moderator'])) return false;
            elseif (isset($this->allowedDirections[$module]) && (empty($this->allowedDirections[$module]) || in_array($controller_action, $this->allowedDirections[$module]))) return false;

            return true;
        };

        return false;
    }

    /**
     * @param \yii\base\Action $event
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($event)
    {
        // gt
        $this->gt = Yii::$app->mv->getGt();
        return parent::beforeAction($event);
    }

    public function getTunes()
    {
        $cache = Yii::$app->cache;
        $syscache = $this->coreSettings->syscache;

        if (!$syscache) $tunes = false;
        else $tunes = $cache->get('tune_' . Yii::$app->language);

        if ($tunes === false)
        {
            $tunes = Tune::find()->indexBy('id')->multilingual()->all();
            $cache->set('tune_' . Yii::$app->language, $tunes);
        }

        return $tunes;
    }

    public function getTune($id, $is_edit = true)
    {
        $edit = '';

        if (!isset($this->tune[$id]))
        {
//            $tune = new Tune();
//            $tune->type = $id;
//            $tune->save();
//
//            $this->tune[$id] = $tune;
//
//            if ($this->coreSettings->syscache) {
//                Yii::$app->cache->set('tune_' . Yii::$app->language, $this->tune);
//            }
        }
        if ($is_edit) $edit = $this->getTuneE($id);
        return $this->tune[$id]->val . $edit;
    }

    public function getTuneE($id, $cl = '')
    {
        $edit = '';

        if ($id && (Yii::$app->user->can('admin') || isset($_GET['gt'])))
        {
            $tune = $this->tune[$id];
            $edit = Html::a('<i style="opacity:0.4" class="fa fa-pencil"></i>', ['/admin/tune/update/', 'id' => $tune->id], ['target' => '_blank', 'class' => 'context-edit context-edit-' . $cl, 'title' => $tune->type,]);
            $edit = "<span style='position: relative'>{$edit}</span>";
        }

        return $edit;
    }

    public function getTuneEG($ids = [], $cl = '')
    {
        $edit = '';

        if (count($ids) && (Yii::$app->user->can('admin') || isset($_GET['gt'])))
        {
            $text = '';
            foreach ($ids as $id)
            {
                $tune = $this->tune[$id];
                $edit = Html::a($tune->type, ['/admin/tune/update/', 'id' => $tune->id], ['target' => '_blank', 'title' => $tune->type,]);
                $text .= '<div>' . $edit . '</div>';
            }

            $edit = '<div class="context-edit group ' . $cl . '">' . $text . '</div>';
        }

        return $edit;
    }

    public static function getOldLangAssoc($lang = null)
    {
        $languages = ['en-US' => 'en', 'ru-RU' => 'ru', 'kz' => 'Kz-kz'];
        return isset($languages[$lang]) ? $languages[$lang] : 'ru';
    }

    public static function getNewLangAssoc($lang = null)
    {
        $languages = ['en' => 'en-US', 'ru' => 'ru-RU', 'kz' => 'Kz-kz'];
        return isset($languages[$lang]) ? $languages[$lang] : 'ru';
    }

    public function getLink()
    {
        $link = Yii::$app->request->url;
        $link = explode('/', $link);
        if (isset($link[1]) && $link[1] == $this->lang) { unset($link[0]); $link[1] = ''; }

        $link = implode('/', $link);
        if (!$link) $link = '/';

        return $link;
    }

    public function throw404()
    {
        throw new NotFoundHttpException(Yii::$app->mv->gt('Page not found.', [], 0));
    }

    public function getLangs()
    {
        $cache = Yii::$app->cache;
        $syscache = $this->coreSettings->syscache;

        if (!$syscache)
        {
            $this->langs = Lang::find()->indexBy('url')->all();
            $cache->set('langs_' . Yii::$app->language, $this->langs);
        }
        else $this->langs = $cache->get('langs_' . Yii::$app->language);


        foreach ($this->langs as $lang)
        {
            $local = $lang->local;
            $url = $lang->url;

            $this->oldLangAssoc[$local] = $url;
            $this->newLangAssoc[$url] = $local;
        }
    }

    public function json()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
    }


    /**
     * @param Dynamic $pagejs
     */
    protected function overrideMeta ($page)
    {
        if (!empty($page->title)) $this->view->title = $page->title;

        $meta = Metadata::find()->where(['data_id' => $page->id, 'data_type' => 0])->multilingual()->one();
        if ($meta) /** @var Metadata $meta */
        {
            if (!empty($meta->description))
            {
                foreach ($this->view->metaTags as $i => $metaTag)
                {
                    if (preg_match('/\"description\"/', $metaTag) == 1)
                    {
                        unset($this->view->metaTags[$i]);
                        $this->view->registerMetaTag(['name' => 'description', 'content' => $meta->description]);
                    }
                }
            }

            if (!empty($meta->keywords))
            {
                foreach ($this->view->metaTags as $i => $metaTag)
                {
                    if (preg_match('/\"keywords\"/', $metaTag) == 1)
                    {
                        unset($this->view->metaTags[$i]);
                        $this->view->registerMetaTag(['name' => 'keywords', 'content' => $meta->keywords]);
                    }
                }
            }
        }
    }

}
