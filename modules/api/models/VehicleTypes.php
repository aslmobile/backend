<?php namespace app\modules\api\models;

use yii\helpers\ArrayHelper;

class VehicleTypes extends \app\models\VehicleType
{
    public static function getTypesList($asArray = false) {
        $list = self::find()->all();
        return $asArray ? ArrayHelper::map($list, 'id', 'title') : $list;
    }
}