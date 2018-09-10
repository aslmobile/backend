<?php
/**
 * Created by PhpStorm.
 * User: demian
 * Date: 20.04.18
 * Time: 10:56
 */

namespace app\modules\admin\models;


class City extends \app\models\City
{
    public static function getStatusList()
    {
        return [
            self::STATUS_DISABLED => \Yii::t('app', "Отключен"),
            self::STATUS_ACTIVE => \Yii::t('app', "Активный")
        ];
    }

}
