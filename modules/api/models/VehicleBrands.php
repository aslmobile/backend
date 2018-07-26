<?php namespace app\modules\api\models;

use yii\helpers\ArrayHelper;

class VehicleBrands extends \app\models\VehicleBrand
{
    public static function getBrandsList($type_id, $asArray = false) {
        $list = self::find()->where(['vehicle_type_id' => $type_id])->all();

        $_list = [];
        if ($list && count ($list) > 0) foreach ($list as $brand) $_list[] = [
            'id' => $brand->id,
            'value' => $brand->title
        ];

        return $asArray ? $_list : $list;
    }
}