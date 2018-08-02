<?php namespace app\modules\admin\models;

class Km extends \app\models\Km
{
    public $settings_accumulation = false;
    public $settings_waste = false;
    public $settings_rate = false;

    public function afterFind()
    {
        parent::afterFind();

        $settings = json_decode($this->settings, true);
        $this->settings_accumulation = $settings['accumulation'];
        $this->settings_waste = $settings['waste'];
        $this->settings_rate = $settings['rate'];
    }

    public function beforeSave($insert)
    {
        $settings = [];
        $input_data = \Yii::$app->request->post('Km');

        $settings['accumulation'] = $input_data['settings_accumulation'];
        $settings['waste'] = $input_data['settings_waste'];
        $settings['rate'] = $input_data['settings_rate'];

        $this->settings = json_encode($settings);

        return parent::beforeSave($insert);
    }
}