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
}
