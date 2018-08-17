<?php namespace app\modules\api\models;

use yii\helpers\ArrayHelper;

class Route extends \app\models\Route
{
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        $array['start_city'] = City::findOne($this->start_city_id)->toArray();
        $array['end_city'] = City::findOne($this->end_city_id)->toArray();
        return $array;
    }
}