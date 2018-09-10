<?php namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property string $modelTitle
 * @property string $sc
 */
class VehicleType extends \app\models\VehicleType
{
    public function getModelTitle()
    {
        return Yii::t('app', "Тип");
    }

    /**
     * Model Special Content
     * @return string
     */
    public function getSc()
    {
        return 'type';
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', "Активный"),
            self::STATUS_DISABLED => Yii::t('app', "Отключен")
        ];
    }

    public static function getTypesList()
    {
        $types = VehicleType::findAll(['status' => VehicleType::STATUS_ACTIVE]);
        return ArrayHelper::map($types, 'id', 'title');
    }
}
