<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "route".
 *
 * @property int $id
 * @property string $title
 * @property int $status
 * @property float $base_tariff
 * @property int $created_at
 * @property int $updated_at
 */
class Route extends \yii\db\ActiveRecord
{
    const
        STATUS_ACTIVE = 1,
        STATUS_DISABLED = 0;

    public static function tableName()
    {
        return 'route';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','base_tariff'], 'required'],
            ['status', 'integer'],
            ['base_tariff', 'number'],
            ['title', 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', "ID"),
            'title'             => Yii::t('app', "Название"),
            'status'            => Yii::t('app', "Статус"),
            'base_tariff'       => Yii::t('app', "Базовый тариф"),
            'created_at'        => Yii::t('app', "Создано"),
            'updated_at'        => Yii::t('app', "Обновлено")
        ];
    }
}
