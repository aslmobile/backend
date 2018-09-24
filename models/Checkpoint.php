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
 * @property int $pid
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
 *
 * @property Checkpoint[] $children
 *
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

    public $children = true;

    public static function tableName()
    {
        return 'checkpoint';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'route'], 'required'],
            ['children', 'safe'],
            [['children'], 'required', 'when' => function ($model) {
                return $model->type == self::TYPE_START;
            }, 'whenClient' => "function (attribute, value) {
                return $('#checkpoint-type').val() == 1;
            }"],
            [['status', 'type', 'weight', 'image', 'country_id', 'region_id', 'city_id', 'route', 'pid'], 'integer'],
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
            'id' => Yii::t('app', "ID"),
            'title' => Yii::t('app', "Название"),
            'status' => Yii::t('app', "Статус"),
            'type' => Yii::t('app', "Тип"),
            'pid' => Yii::t('app', "Начальная остановка маршрута"),
            'children' => Yii::t('app', "Городские остановки"),
            'weight' => Yii::t('app', "Порядок"),
            'image' => Yii::t('app', "Изображение"),
            'country_id' => Yii::t('app', "Страна"),
            'region_id' => Yii::t('app', "Регион"),
            'city_id' => Yii::t('app', "Город"),
            'route' => Yii::t('app', "Маршрут"),
            'created_at' => Yii::t('app', "Создано"),
            'updated_at' => Yii::t('app', "Обновлено"),
            'latitude' => Yii::t('app', "Широта"),
            'longitude' => Yii::t('app', "Долгота"),
        ];
    }

    public static function getAllChildren($where = null)
    {
        $q = self::find()->where(['type' => self::TYPE_STOP, 'status' => self::STATUS_ACTIVE])->select(['title', 'id']);
        if ($where) {
            $q->andWhere($where);
        }

        return $q->orderBy(['title' => SORT_ASC])->indexBy('id')->column();
    }

    public function getChildrenR()
    {
        return $this->hasMany(self::class, ['pid' => 'id'])
            ->andWhere(['status' => self::STATUS_ACTIVE, 'type' => self::TYPE_STOP])
            ->orderBy(['weight' => SORT_ASC]);
    }

    public function getRouteModel()
    {
        return Route::findOne(['id' => $this->route]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->type == self::TYPE_START) {
            Checkpoint::updateAll(['pid' => null], ['pid' => $this->id, 'type' => self::TYPE_STOP]);
            Checkpoint::updateAll(['pid' => $this->id], ['id' => $this->children, 'type' => self::TYPE_STOP]);
        }
        parent::afterSave($insert, $changedAttributes);
    }

}
