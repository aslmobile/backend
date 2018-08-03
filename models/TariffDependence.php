<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "tariff_dependence".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $route_id
 * @property integer $start_checkpoint_id
 * @property integer $end_checkpoint_id
 * @property integer $vehicle_type_id
 * @property double $base_tariff
 * @property double $base_rate
 */
class TariffDependence extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariff_dependence';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'status', 'route_id', 'start_checkpoint_id', 'end_checkpoint_id', 'vehicle_type_id'], 'integer'],
            [['base_tariff', 'base_rate'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'created_at' => Yii::$app->mv->gt('Created At', [], 0),
            'updated_at' => Yii::$app->mv->gt('Updated At', [], 0),
            'status' => Yii::$app->mv->gt('Status', [], 0),
            'route_id' => Yii::$app->mv->gt('Route ID', [], 0),
            'start_checkpoint_id' => Yii::$app->mv->gt('Start Checkpoint ID', [], 0),
            'end_checkpoint_id' => Yii::$app->mv->gt('End Checkpoint ID', [], 0),
            'vehicle_type_id' => Yii::$app->mv->gt('Vehicle Type ID', [], 0),
            'base_tariff' => Yii::$app->mv->gt('Base Tariff', [], 0),
            'base_rate' => Yii::$app->mv->gt('Base Rate', [], 0),
        ];
    }

}
