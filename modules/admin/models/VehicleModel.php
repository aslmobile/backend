<?php namespace app\modules\admin\models;

use Yii;

/**
 * @property string $modelTitle
 * @property string $sc
 */
class VehicleModel extends \app\models\VehicleModel
{
    public function getModelTitle()
    {
        return Yii::t('app', "Модель");
    }

    /**
     * Model Special Content
     * @return string
     */
    public function getSc()
    {
        return 'model';
    }

    public function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', "Активный"),
            self::STATUS_DISABLED => Yii::t('app', "Отключен")
        ];
    }
}