<?php namespace app\modules\admin\models;

use Yii;

/**
 * @property string $modelTitle
 * @property string $sc
 */
class VehicleBrand extends \app\models\VehicleBrand
{
    public function getModelTitle()
    {
        return Yii::t('app', "Бренд");
    }

    /**
     * Model Special Content
     * @return string
     */
    public function getSc()
    {
        return 'brand';
    }

    public function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', "Активный"),
            self::STATUS_DISABLED => Yii::t('app', "Отключен")
        ];
    }
}