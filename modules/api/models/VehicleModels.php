<?php namespace app\modules\api\models;

use yii\helpers\ArrayHelper;

class VehicleModels extends \app\models\VehicleModel
{
    public static function getModelsList($brand_id, $asArray = false) {
        $list = self::find()->where(['vehicle_brand_id' => $brand_id])->all();
        return $asArray ? ArrayHelper::map($list, 'id', 'title') : $list;
    }
}