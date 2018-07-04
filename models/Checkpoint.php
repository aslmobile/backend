<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "checkpoint".
 *
 * @property int $id
 * @property string $title
 * @property int $status
 * @property int $type
 * @property int $weight
 * @property int $image
 * @property int $country_id
 * @property int $region_id
 * @property int $city_id
 * @property int $route
 * @property float $latitude
 * @property float $longitude
 * @property int $created_at
 * @property int $updated_at
 */
class Checkpoint extends \yii\db\ActiveRecord
{
    const
        STATUS_ACTIVE = 1,
        STATUS_DISABLED = 0;

    const
        TYPE_START = 1,
        TYPE_END = 2,
        TYPE_STOP = 3;

    public static function tableName()
    {
        return 'checkpoint';
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
            [['title','route'], 'required'],
            [['status', 'type', 'weight', 'image', 'country_id', 'region_id', 'city_id', 'route'], 'integer'],
            [['latitude', 'longitude'], 'number'],
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
            'type'              => Yii::t('app', "Тип"),
            'weight'            => Yii::t('app', "Вес сортировки"),
            'image'             => Yii::t('app', "Изображение"),
            'country_id'        => Yii::t('app', "Страна"),
            'region_id'         => Yii::t('app', "Регион"),
            'city_id'           => Yii::t('app', "Город"),
            'route'             => Yii::t('app', "Маршрут"),
            'created_at'        => Yii::t('app', "Создано"),
            'updated_at'        => Yii::t('app', "Обновлено")
        ];
    }

    public function getRouteModel()
    {
        return Route::findOne(['id' => $this->route]);
    }
}
