<?php namespace app\modules\api\models;

use yii\helpers\ArrayHelper;

class VehicleModels extends \app\models\VehicleModel
{
    public static function getModelsList($brand_id, $asArray = false) {
        $list = self::find()->where(['vehicle_brand_id' => $brand_id])->all();

        $_list = [];

        /** @var \app\models\VehicleModel $model */
        if ($list && count ($list) > 0) foreach ($list as $model) $_list[] = [
            'id' => $model->id,
            'value' => $model->title,
            'seats' => $model->max_seats
        ];

        return $asArray ? $_list : $list;
    }
}