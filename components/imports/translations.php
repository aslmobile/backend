<?

use app\modules\admin\models\Translations;
use app\modules\admin\models\TranslationsLang;

set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$start = microtime(true);

$orlang = 'en';

//$url = 'https://docs.google.com/spreadsheets/d/1xD2CfKt3x9yF-yNHilsf8IhzDt-e29eU-rT8kyKKcy0/export?format=csv&id=1xD2CfKt3x9yF-yNHilsf8IhzDt-e29eU-rT8kyKKcy0';
$backlink = '<a href="?">Вернуться назад</a>';
if (!isset($_GET['proceed'])) {
    $_GET['proceed'] = '';//ffs
}
switch ($_GET['proceed']) {
    case 'import':
        $file = @file_get_contents($url);
        if ($file) {
            $fname = __DIR__ . '/dl' . time();
            file_put_contents($fname, $file);
            $row = 0;

            $keys = [];
            $orlang_key = NULL;
            $replace_key = NULL;

            if (($handle = fopen($fname, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($row) {
                        $originalTexts = Translations::find()
                            ->where(['original_val' => trim($data[$orlang_key])])
                            ->indexBy('id')
                            ->all();
                        $nv = $data[$orlang_key];
                        if (trim($data[$replace_key])) {
                            $nv = trim($data[$replace_key]);
                        }
                        foreach ($originalTexts as $ot) {
                            $ot->val = $nv;
                            $ot->save();
                        }
                        foreach ($keys as $k => $lang_key) {
                            $translateText = $data[$k];
                            TranslationsLang::updateAll(
                                [
                                    'val' => trim($translateText)
                                ],
                                [
                                    'translations_id' => array_keys($originalTexts),
                                    'language' => $lang_key
                                ]
                            );
                        }
                    } else {
                        foreach ($data as $k => $v) {
                            $v = trim($v);
                            if (mb_strlen($v) == 2) {
                                $lang_key = mb_strtolower($v);
                                if ($lang_key == $orlang) {
                                    $orlang_key = $k;
                                } else {
                                    $keys[$k] = $lang_key;
                                }
                            } elseif ($v == 'NEED2Replace') {
                                $replace_key = $k;
                            }
                        }
                    }
                    $row++;
                }
                fclose($handle);
                echo 'Импорт произведен успешно за ' . round(microtime(true) - $start) . ' секунд. <a href="?proceed=import">Запустить еще раз</a> или ' . $backlink;
            } else {
                echo 'Ошибка чтения файла ' . __DIR__ . '/' . $fname . '. ' . $backlink;
            }
            @unlink($fname);
            $one = Translations::find()->one();
            $one->save();
        } else {
            echo 'Ошибка получения <a href="' . $url . '" target="_blank">файла из google таблиц. ' . $backlink;
        }

        break;

    case 'empty':
        $file = @file_get_contents($url);
        if ($file) {
            $fname = 'dl' . time();
            file_put_contents($fname, $file);
            $row = 0;

            $orlang_key = NULL;

            $translations = Translations::find()->indexBy('original_val')->all();
            foreach ($translations as $k => $tra) {
                unset($translations[$k]);
                $translations[mb_strtolower($k)] = $tra;
            }
            if (($handle = fopen($fname, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($row) {
                        $tr = mb_strtolower(trim($data[$orlang_key]));
                        if (isset($translations[$tr])) {
                            unset($translations[$tr]);
                        }
                    } else {
                        foreach ($data as $k => $v) {
                            $v = trim($v);
                            if (mb_strlen($v) == 2) {
                                $lang_key = mb_strtolower($v);
                                if ($lang_key == $orlang) {
                                    $orlang_key = $k;
                                }
                            }
                        }
                    }
                    $row++;
                }
                fclose($handle);
                foreach ($translations as $k => $tra) {
                    unset($translations[$k]);
                    $translations[$tra->original_val] = $tra;
                }
                echo 'Найдено непереведенных строк ' . count(array_keys($translations)) . '. <a href="?proceed=import">Запустить еще раз</a> или ' . $backlink;
                echo '<div>' . implode('</div><div>', array_keys($translations)) . '</div>';
            } else {
                echo 'Ошибка чтения файла ' . __DIR__ . '/' . $fname . '. ' . $backlink;
            }
            @unlink($fname);
        } else {
            echo 'Ошибка получения <a href="' . $url . '" target="_blank">файла из google таблиц. ' . $backlink;
        }

        break;

    default:
        ?>
        <div style="border: 1px solid #ccc; padding:30px;margin: 30px">
            <div>
                Произвести импорт переводов из <a
                        href="https://docs.google.com/spreadsheets/d/1xD2CfKt3x9yF-yNHilsf8IhzDt-e29eU-rT8kyKKcy0/edit#gid=0"
                        target="_blank">google таблиц</a>
            </div>
            <div>
                <a href="?proceed=import">Начать</a>
            </div>
            <div>
                <a href="javascript:void(0)" onclick="window.close()">Отмена</a>
            </div>
        </div>
        <div style="border: 1px solid #ccc; padding:30px;margin: 30px">
            <div>
                Вывод строк, которых нет в <a
                        href="https://docs.google.com/spreadsheets/d/1xD2CfKt3x9yF-yNHilsf8IhzDt-e29eU-rT8kyKKcy0/edit#gid=0"
                        target="_blank">документе переводов google таблиц</a>
            </div>
            <div>
                <a href="?proceed=empty">Начать</a>
            </div>
            <div>
                <a href="javascript:void(0)" onclick="window.close()">Отмена</a>
            </div>
        </div>
        <?
        break;

}
?>
