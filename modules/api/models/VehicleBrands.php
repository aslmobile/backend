<?php

namespace app\modules\api\models;

class VehicleBrands extends \app\models\VehicleBrand
{
    public static function getBrandsList($asArray = false)
    {

        $list = self::find()->all();

        $_list = [];

        /** @var \app\models\VehicleBrand $brand */
        if ($list && count($list) > 0) foreach ($list as $brand) $_list[] = [
            'id' => $brand->id,
            'value' => $brand->title,
            'seats' => $brand->max_seats
        ];

        return $asArray ? $_list : $list;

    }
}
