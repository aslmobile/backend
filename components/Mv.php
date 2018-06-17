<?php
/**
 * Created by PhpStorm.
 * User: Misha
 * Date: 16.02.2016
 * Time: 17:36
 */

namespace app\components;

use app\models\Like;
use app\models\Mailtpl;
use app\models\Rating;
use app\modules\admin\models\Block;
use app\modules\admin\models\Currency;
use app\modules\admin\models\MetaTags;
use app\modules\admin\models\Translations;
use app\modules\admin\models\TranslationsLang;
use dosamigos\transliterator\TransliteratorHelper;
use Yii;
use yii\base\Component;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\widgets\LinkPager;

/**
 * Class Mv
 * @package app\components
 */
class Mv extends Component
{


    public $gts = [];

    public function ic($preset, $path, $class = '')
    {
        return Yii::$app->imageCache->imgSrc($_SERVER['DOCUMENT_ROOT'] . '/web' . $path, $preset, ['class' => $class]);
    }

    public function ae($model, $id)
    {
        if (Yii::$app->user->can('admin')) {
            return '<a class="editbutton" href="/admin/' . $model . '/update/' . $id . '" target="_blank"><i class="fa fa-pencil"></i></a>';
        }
    }

    /**
     * @param $key
     * @param array $ph
     * @param int $is_edit
     * @return string
     */
    public function curPageGt()
    {
        if (Yii::$app->user->can('admin') || isset($_GET['gt'])) {
            $out = '';
            foreach ($this->gts as $gt) {
                $out .= '<div><a href="/admin/translations/update/' . $gt['link'] . '">' . $gt['title'] . '</a></div>';
            }

            //return '<div class="curPageGt deftrans">'.$out.'</div>';

            $ret = <<<FRE
<button type="button" class="btn btn-default btn-lg trans_gt" data-toggle="modal" data-target="#gtmod"><i class="fa fa-file-text"></i></button>
  <div class="modal fade" id="gtmod" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Translation phrases</h4>
        </div>
        <div class="modal-body">
          $out
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
FRE;
            return $ret;
        }
    }

    /**
     * @param $key
     * @param array $ph
     * @param int $is_edit
     * @return string
     */
    public function gt($key, $ph = array(), $is_edit = 1)
    {


        if (!Yii::$app->controller || !isset(Yii::$app->controller->coreSettings)) {
            return $key;
        }
        //return Yii::t('app',$key);
        $model = array();

        $trace = debug_backtrace();
        $file = current($trace);
        $file = $file['file'];
        if (strpos($file, 'views') === false) {
            $nkey = Yii::$app->controller->id . '_' . Yii::$app->controller->action->id . '_' . strtolower(basename(__FILE__)) . '_' . $key;
        } else {
            if (strpos($file, '\\views\\')) {
                $uurl = explode('\\views\\', $file);
            } else {
                $uurl = explode('/views/', $file);
            }
            $nkey = $uurl[1] . '_' . $key;
        }

        if (!isset(Yii::$app->controller->gt[$nkey])) {
            $orlang = Yii::$app->language;
            Yii::$app->language = Yii::$app->params['sourceLanguage'];
            $ortrans = Yii::t('app', $key);
            $basetranse = Yii::t('app', $key);
            $trans = Translations::find()->where('trans_key=:tk AND val=:val', array(':tk' => $nkey, ':val' => $ortrans))->one();

            if (!$trans) {
                $trans = new Translations;
                $trans->trans_key = $nkey;
                $trans->val = $ortrans;
            }

            $trans->descr = Yii::$app->controller->id . '_' . Yii::$app->controller->action->id . '_' . strtolower(basename(__FILE__));
            $link_dirty = explode('?', $_SERVER['REQUEST_URI']);
            $link = preg_replace('/[0-9]+/', '', $link_dirty[0]);
            $trans->url = $link;
            if ($trans->save()) {
                $model[] = $trans;
                $tid = $trans->id;

                foreach (Yii::$app->controller->coreSettings->languages as $k => $v) {

                    $xtrans = TranslationsLang::find()->where('translations_id=:tid AND language=:lid', array(':tid' => $tid, ':lid' => $v))->one();
                    if (!$xtrans) {
                        $xtrans = new TranslationsLang();

                        Yii::$app->language = Controller::getOldLangAssoc($v);
                        $ortrans = Yii::t('app', $key);
                        $xtrans->val = $ortrans;
                    }
                    $xtrans->translations_id = $tid;
                    $xtrans->language = $v;

                    $xtrans->save(false);
                }
            }

            Yii::$app->language = $orlang;
        } else {
            $model[0] = Yii::$app->controller->gt[$nkey];
        }

        if (count($ph)) {
            $model[0]['val'] = Yii::t('app', $model[0]['val'], $ph);
        }

        if ($is_edit && (Yii::$app->user->can('admin') || isset($_GET['gt'])) && Yii::$app->controller->module->id != 'admin') {
            if (!isset($model[0])) {
                var_dump($model, $key);
                die();
            }

            $edit = '<a class="context-edit" href="/admin/translations/update/' . $model[0]['id'] . '"><i class="fa fa-pencil"></i></a>';
        } else {
            $edit = '';
        }

        if ((Yii::$app->user->can('admin') || isset($_GET['gt'])) && count($model)) {
            if (!isset($this->gts[$model[0]['id']])) {
                $this->gts[$model[0]['id']] = ['link' => $model[0]['id'], 'title' => $model[0]['val']];
            }
        }

        return (!count($model)) ? $key : nl2br($model[0]['val']) . $edit;
    }

    /**
     * @param $st
     * @return mixed
     */
    public static function transliterate($st)
    {
        return TransliteratorHelper::process($st, '', 'en');
    }

    public static function transliterateUrl($st)
    {
        $st = strip_tags($st);
        $st = self::transliterate($st);
        $st = trim(mb_strtolower($st));
        $st = str_replace([' '], '-', $st);
        $st = preg_replace("/[^a-z0-9\/_-]+/", '', $st);
        return $st;
    }

    /**
     * set meta tags
     */
    public function metaSetter($title = null, $description = null)
    {
        return false;

        $currentUrl = Url::current();
        foreach (Yii::$app->controller->coreSettings->languages as $k => $v) {
            $currentUrl = str_replace('/' . $v . '/', '', $currentUrl);
        }
        $currentUrl = ($currentUrl == '/') ? $currentUrl : ltrim($currentUrl, '/');
        $meta = MetaTags::find()->where(['url' => $currentUrl])->one();
        if (!empty($meta)) {
            Yii::$app->params['defTitle'] = $meta->title;
            Yii::$app->params['defDescription'] = $meta->description;
        }
        if (!empty($title)) {
            Yii::$app->params['defTitle'] = $title;
        }
        if (!empty($description)) {
            Yii::$app->params['defDescription'] = $description;
        }
        Yii::$app->view->registerMetaTag([
            'name' => 'title',
            'content' => Yii::$app->params['defTitle'],
        ], "main_title");
        Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => Yii::$app->params['defDescription'],
        ], 'main_description');


    }

    public function mailTo($email, $params, $lang = '')
    {

        if (!$lang && Yii::$app->params['sourceLanguage'] != Yii::$app->language) {
            $lang = self::getOldLangAssoc(Yii::$app->language);
        }
        if ($lang == self::getOldLangAssoc(Yii::$app->language)) {
            $lang = '';
        }

        $data = Yii::$app->mv->buildMail($params, $lang);

        if ($data['title'] && $data['descr']) {

            $subject = $data['title'];
            $message = $data['descr'];

            return Yii::$app->mv->sendMail($email, $subject, $message);

        }
    }

    public function sendMail($email, $subject, $message)
    {
        $message = Yii::$app->controller->renderPartial('//layouts/_mailtpl', ['subject' => $subject, 'message' => $message]);

        $subject = '=?utf-8?B?' . base64_encode($subject) . '?=';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $email . '<' . $email . '>' . "\r\n";
        $headers .= 'From: ' . Yii::$app->params['defTitle'] . '<' . Yii::$app->params['adminEmail'] . '>' . "\r\n";
        return mail($email, $subject, $message, $headers);
    }

    public function buildMail($params, $lang = '')
    {
        $data = ['title' => '', 'descr' => ''];

        $rep = [];

        if (isset($params['rep'])) {
            $rep = $params['rep'];
        }

        foreach ($rep as $k => $r) {
            $rep['{' . $k . '}'] = $r;
            unset($rep[$k]);
        }

        if (isset($params['type'])) {
            $mailtpl = Mailtpl::find()->where(['type' => $params['type']])->multilingual()->one();
            if ($mailtpl) {

                $title = 'title';
                $descr = 'descr';

                if ($lang) {
                    $title = 'title_' . $lang;
                    $descr = 'descr_' . $lang;
                }

                $data['title'] = $mailtpl->$title;
                $data['descr'] = str_replace(array_keys($rep), array_values($rep), $mailtpl->$descr);

            }
        }

        $data['descr'] = str_replace('/admin/mailtpl/update/', '', $data['descr']);
        $data['descr'] = str_replace('/admin/mailtpl/', 'http://' . $_SERVER['HTTP_HOST'], $data['descr']);

        return $data;
    }

    public function validatePhone($phone)
    {
        $test = preg_replace('/[^0-9.]+/', '', $phone);
        if ($phone && mb_strlen($test) == 12) {
            return $test;
        }
        return false;
    }

    public function preloadImage()
    {

        $return = [];

        $key = Yii::$app->request->get('key');
        $subkey = Yii::$app->request->get('subkey');
        $preset = Yii::$app->request->get('preset');
        $rand = Yii::$app->request->post('rand');

        $field_name = $key;
        if ($subkey) {
            $field_name .= '[' . $subkey . ']';
        }

        $files = UploadedFile::getInstanceByName($field_name);

        if (!$files) {
            $files = UploadedFile::getInstancesByName($field_name);
        }

        if ($files) {
            $isone = 0;
            if (!is_array($files)) {
                $files = [$files];
                $isone = 1;
            }

            foreach ($files as $k => $file) {
                $ext = explode('.', $file->name);
                $ext = mb_strtolower(end($ext));

                $allowed_ext = ['jpg', 'jpeg', 'png'];
                $allowed_formats = ['image/png', 'image/jpeg'];

                if (in_array($ext, $allowed_ext) && in_array($file->type, $allowed_formats)) {

                    if ($file->size < 5 * 1024 * 1024) {

                        $file_url = '/preload/' . md5($rand) . '_' . $isone . '_' . $k . '.' . $ext;

                        $full_file_url = $_SERVER['DOCUMENT_ROOT'] . '/web' . $file_url;


                        if ($file->saveAs($full_file_url)) {
                            $return['original'][] = $file_url;
                            if ($preset) {
                                $uploaded_file = Yii::$app->mv->ic($preset, $file_url);
                                @unlink($_SERVER['DOCUMENT_ROOT'] . '/web' . $uploaded_file);
                                $uploaded_file = Yii::$app->mv->ic($preset, $file_url);
                            } else {
                                $uploaded_file = $file_url;
                            }
                            $uploaded_file .= '?' . time();
                            $return['file'][] = $uploaded_file;
                        } else {
                            $return['popup'] = Yii::$app->mv->gt('Error uploading one or more images, they were not loaded.');
                        }

                    } else {
                        $return['popup'] = Yii::$app->mv->gt('One or more of the selected images have size greater than 5Mb, they were not loaded.');
                    }

                } else {
                    $return['popup'] = Yii::$app->mv->gt('One or more selected images have an invalid format, they were not loaded. Acceptable formats: {ext}', ['ext' => implode(', ', $allowed_ext)]);
                }
            }

        }

        return $return;

    }

    public function route($route)
    {
        return Url::toRoute($route);
    }

    public static function pageData(&$model, $limit = 10)
    { //формирование пагинаций

        $count = $model->count();

        $page = 1;
        if (Yii::$app->request->get('page')) {
            $page = (int)Yii::$app->request->get('page');
            if ($page < 1) {
                $page = 1;
            }
        }

        $model->offset($limit * ($page - 1))
            ->limit($limit);

        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit, 'defaultPageSize' => $limit]);
        $pager = LinkPager::widget([
            'pagination' => $pagination,
            'options' => ['class' => 'pagination clearfix'],
            'linkOptions' => ['class' => 'btn-pagination'],
        ]);
        return [
            'pager' => $pager,
            'count' => $count,
        ];

    }

    public static function socketSend($message = [])
    {
        $host = $_SERVER['HTTP_HOST'];  //where is the websocket server
        $port = Yii::$app->params['ws']['backPort'];
        $local = "http://" . $_SERVER['HTTP_HOST'];  //url where this script run

        $message['data'] = [];
        $message['data']['server'] = md5('s' . md5('SOCKET_HASH' . md5('s')));
        $data = json_encode($message);  //data to be send

        $head = "GET / HTTP/1.1" . "\r\n" .
            "Upgrade: WebSocket" . "\r\n" .
            "Connection: Upgrade" . "\r\n" .
            "Origin: $local" . "\r\n" .
            "Host: $host" . "\r\n" .
            "Content-Length: " . strlen($data) . "\r\n" . "\r\n";
        //WebSocket handshake
        $sock = fsockopen($host, $port, $errno, $errstr, 2);
        fwrite($sock, $head) or die('error:' . $errno . ':' . $errstr);
        $headers = fread($sock, 2000);
        fwrite($sock, "\x00$data\xff") or die('error:' . $errno . ':' . $errstr);
        $wsdata = fread($sock, 2000);  //receives the data included in the websocket package "\x00DATA\xff"
        fclose($sock);
    }

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
                $data[$m->trans_key] = array('id' => $m->id, 'val' => $m->val);
            }
            $cache->set('gt_' . Yii::$app->language, $data);
        }
        return $data;
    }

    public function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

}
