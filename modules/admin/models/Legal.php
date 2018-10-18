<?php namespace app\modules\admin\models;

use Yii;

/**
 * Vehicles represents the model `\app\models\Legal`.
 */
class Legal extends \app\models\Legal
{
    public static function getTypes()
    {
        return [
            self::TYPE_DRIVER => Yii::t('app', "Водитель"),
            self::TYPE_PASSENGER => Yii::t('app', "Пассажир")
        ];
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
