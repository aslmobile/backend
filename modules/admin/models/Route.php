<?php namespace app\modules\admin\models;

use Yii;

class Route extends \app\models\Route
{
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE     => Yii::t('app', "Активный"),
            self::STATUS_DISABLED   => Yii::t('app', "Не активный")
        ];
    }

    public function getStartCity()
    {
        return \app\modules\api\models\City::findOne($this->start_city_id);
    }

    public function getEndCity()
    {
        return \app\modules\api\models\City::findOne($this->end_city_id);
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        $array['start_city'] = $this->startCity ? $this->startCity->toArray() : null;
        $array['end_city'] = $this->endCity ? $this->endCity->toArray() : null;

        return $array;
    }
}
