<?php namespace app\modules\admin\models;


/**
 * Class Km
 * @package app\modules\admin\models
 *
 * @property array $settings_accumulation
 * @property array $settings_waste
 */

class Km extends \app\models\Km
{

    public function afterFind()
    {
        parent::afterFind();

        $settings = json_decode($this->settings, true);

        array_walk($settings['accumulation'], function (&$item) {
            $start = strlen($item['from']) - 2;
            $item['from'] = substr_replace(strval($item['from']), ':', $start, 0);
            $start = strlen($item['to']) - 2;
            $item['to'] = substr_replace(strval($item['to']), ':', $start, 0);
            $item['route'] = intval($item['route']);
            $route = Route::findOne($item['route']);
            empty($route)?:$item += ['title' => $route->title];
            $item['rate'] = doubleval($item['rate']);
            array_walk($item['days'], function (&$day) { $day = intval($day); });
            return $item;
        });

        array_walk($settings['waste'], function (&$item) {
            $start = strlen($item['from']) - 2;
            $item['from'] = substr_replace(strval($item['from']), ':', $start, 0);
            $start = strlen($item['to']) - 2;
            $item['to'] = substr_replace(strval($item['to']), ':', $start, 0);
            $item['route'] = intval($item['route']);
            $route = Route::findOne($item['route']);
            empty($route)?:$item += ['title' => $route->title];
            array_walk($item['days'], function (&$day) { $day = intval($day); });
            return $item;
        });

        $this->settings_accumulation = $settings['accumulation'];
        $this->settings_waste = $settings['waste'];
    }

    public function beforeSave($insert)
    {
        $settings = [];
        $input_data = \Yii::$app->request->post('Km');

        if (!empty($input_data['settings_accumulation'])) {
            $settings['accumulation'] = $input_data['settings_accumulation'];
            array_walk($settings['accumulation'], function (&$item) {
                $item['from'] = intval(str_replace(':', '', $item['from']));
                $item['to'] = intval(str_replace(':', '', $item['to']));
                $item['route'] = intval($item['route']);
                $item['rate'] = doubleval($item['rate']);
                array_walk($item['days'], function (&$day) { $day = intval($day); });
                return $item;
            });
        } else $settings['accumulation'] = [];

        if (!empty($input_data['settings_waste'])) {
            $settings['waste'] = $input_data['settings_waste'];
            array_walk($settings['waste'], function (&$item) {
                $item['from'] = intval(str_replace(':', '', $item['from']));
                $item['to'] = intval(str_replace(':', '', $item['to']));
                $item['route'] = intval($item['route']);
                array_walk($item['days'], function (&$day) { $day = intval($day); });
                return $item;
            });
        } else $settings['waste'] = [];

        $this->settings = json_encode($settings);

        return parent::beforeSave($insert);
    }
}
