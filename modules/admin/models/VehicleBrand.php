<?php namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;

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

    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', "Активный"),
            self::STATUS_DISABLED => Yii::t('app', "Отключен")
        ];
    }

    public static function getBrandsList()
    {
        $types = VehicleBrand::findAll(['status' => VehicleBrand::STATUS_ACTIVE]);
        return ArrayHelper::map($types, 'id', 'title');
    }
}
