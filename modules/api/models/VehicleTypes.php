<?php namespace

app\modules\api\models;

class VehicleTypes extends \app\models\VehicleType
{
    public static function getTypesList($asArray = false)
    {
        $list = self::find()->all();

        $_list = [];

        /** @var \app\models\VehicleType $type */
        if ($list && count($list) > 0) foreach ($list as $type) $_list[] = [
            'id' => $type->id,
            'value' => $type->title,
            'seats' => $type->max_seats
        ];

        return $asArray ? $_list : $list;
    }
}
