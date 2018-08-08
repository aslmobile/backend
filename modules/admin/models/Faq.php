<?php namespace app\modules\admin\models;

use Yii;

/**
 * Vehicles represents the model `\app\models\Faq`.
 */
class Faq extends \app\models\Faq
{
    public static function getTypes()
    {
        return [
            self::TYPE_DRIVER => Yii::t('app', "Водитель"),
            self::TYPE_PASSENGER => Yii::t('app', "Пассажир")
        ];
    }
}
