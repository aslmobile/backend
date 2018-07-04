<?php namespace app\modules\admin\models;

use Yii;

class Checkpoint extends \app\models\Checkpoint
{
    public $address;

    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE     => Yii::t('app', "Активная"),
            self::STATUS_DISABLED   => Yii::t('app', "Отключено")
        ];
    }

    public static function getTypesList()
    {
        return [
            self::TYPE_START    => Yii::t('app', "Начальная точка"),
            self::TYPE_END      => Yii::t('app', "Конечная точка"),
            self::TYPE_STOP     => Yii::t('app', "Остановка")
        ];
    }
}
