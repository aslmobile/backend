<?php namespace app\modules\admin\models;

use Yii;

/**
 * @property string $modelTitle
 */
class VehicleModel extends \app\models\VehicleModel
{
    public function getModelTitle()
    {
        return Yii::t('app', "Модель");
    }
}