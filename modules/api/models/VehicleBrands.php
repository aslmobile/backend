<?php namespace app\modules\api\models;

use yii\helpers\ArrayHelper;

class VehicleBrands extends \app\models\VehicleBrand
{
    public static function getBrandsList($type_id, $asArray = false) {
        $list = self::find()->where(['vehicle_type_id' => $type_id])->all();
        return $asArray ? ArrayHelper::map($list, 'id', 'title') : $list;
    }
}