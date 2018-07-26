<?php namespace app\modules\api\models;

use yii\helpers\ArrayHelper;

class VehicleTypes extends \app\models\VehicleType
{
    public static function getTypesList($asArray = false) {
        $list = self::find()->all();

        $_list = [];
        if ($list && count ($list) > 0) foreach ($list as $type) $_list[] = [
            'id' => $type->id,
            'value' => $type->title
        ];

        return $asArray ? $_list : $list;
    }
}