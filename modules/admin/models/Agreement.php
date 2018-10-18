<?php namespace app\modules\admin\models;

use Yii;

/**
 * Vehicles represents the model `\app\models\Agreement`.
 */
class Agreement extends \app\models\Agreement
{
    public static function getTypes()
    {
        return [
            self::TYPE_DRIVER => Yii::t('app', "Водитель"),
            self::TYPE_PASSENGER => Yii::t('app', "Пассажир")
        ];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);
        return $array;
    }

    public function afterFind()
    {
        $this->content = json_decode($this->content, true);
        parent::afterFind();
    }

    public function beforeSave($insert)
    {
        $this->content = json_encode($this->content);
        return parent::beforeSave($insert);
    }

}
