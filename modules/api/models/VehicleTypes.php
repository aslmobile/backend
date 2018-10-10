<?php namespace

app\modules\api\models;

class VehicleTypes extends \app\models\VehicleType
{
    public static function getTypesList($asArray = false, $with_any = false)
    {
        $list = self::find()->all();

        $_list = [];
        if ($with_any) $_list[] = \Yii::$app->mv->gt("Любой", [], false);

        /** @var \app\models\VehicleType $type */
        if ($list && count($list) > 0) {
            foreach ($list as $type) $_list[] = [
                'id' => $type->id,
                'value' => $type->title,
                'seats' => $type->max_seats
            ];
        }

        return $asArray ? $_list : $list;
    }
}
