<?php namespace app\models;

use app\components\NotNullBehavior;
use app\components\Socket\SocketPusher;
use app\modules\api\models\Users;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "line".
 *
 * @property int $id
 * @property int $status
 * @property int $driver_id
 * @property int $vehicle_id
 * @property integer $vehicle_type_id
 * @property bool $ready
 * @property float $tariff
 * @property int $route_id
 * @property int $startpoint
 * @property int $endpoint
 * @property int $seats
 * @property int $freeseats
 * @property int $maxseats
 * @property int $starttime
 * @property int $endtime
 * @property int $cancel_reason
 * @property int $penalty
 * @property string $angle
 * @property string $position
 * @property string $path
 * @property int $created_at
 * @property int $updated_at
 *
 * @property \app\models\User $driver
 * @property \app\models\Vehicles $vehicle
 * @property \app\models\Route $route
 * @property \app\models\Checkpoint $startPoint
 * @property \app\models\Checkpoint $endPoint
 */
class Line extends \yii\db\ActiveRecord
{
    const
        STATUS_QUEUE = -1,
        STATUS_CANCELED = 0,
        STATUS_IN_PROGRESS = 1,
        STATUS_WAITING = 2,
        STATUS_FINISHED = 3;

    public $ready = false;
    public $oldStatus;

    public function init()
    {
        parent::init();
        $this->oldStatus = $this->status;
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_QUEUE => Yii::t('app', "В очереди"),
            self::STATUS_CANCELED => Yii::t('app', "Отменена"),
            self::STATUS_WAITING => Yii::t('app', "Ожидает посадки"),
            self::STATUS_IN_PROGRESS => Yii::t('app', "В пути"),
            self::STATUS_FINISHED => Yii::t('app', "Завершена")
        ];
    }

    public static function tableName()
    {
        return 'line';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            NotNullBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'driver_id',
                    'vehicle_id',
                    'status',
                    'route_id',
                    'seats',
                    'freeseats',
                    'startpoint',
                    'endpoint',
                    'tariff'
                ],
                'required'
            ],

            [
                [
                    'status',
                    'driver_id',
                    'vehicle_id',
                    'vehicle_type_id',
                    'route_id',
                    'startpoint',
                    'endpoint',
                    'seats',
                    'freeseats',
                    'maxseats',
                    'cancel_reason',
                    'penalty'
                ],
                'integer'
            ],

            [
                [
                    'tariff'
                ],
                'number'
            ],

            [
                [
                    'path'
                ],
                'string'
            ],

            [
                [

                    'starttime',
                    'endtime',

                    'angle', 'position'
                ],
                'safe'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'driver_id' => Yii::t('app', "Водитель"),
            'vehicle_id' => Yii::t('app', "Автомобиль"),
            'vehicle_type_id' => Yii::t('app', "Тип автомобиля"),
            'route_id' => Yii::t('app', "Маршрут"),
            'startpoint' => Yii::t('app', "Начальная точка"),
            'endpoint' => Yii::t('app', "Конечная точка"),
            'tariff' => Yii::t('app', "Тариф"),
            'status' => Yii::t('app', "Статус"),
            'seats' => Yii::t('app', "Мест"),
            'freeseats' => Yii::t('app', "Свободных мест"),
            'maxseats' => Yii::t('app', "Максимум мест"),
            'starttime' => Yii::t('app', "Время отправления"),
            'endtime' => Yii::t('app', "Время прибытия"),
            'cancel_reason' => Yii::t('app', "Причина отмены"),
            'penalty' => Yii::t('app', "Наложен штраф"),
            'angle' => Yii::t('app', "Угол поворота"),
            'position' => Yii::t('app', "GEO позиция"),
            'path' => Yii::t('app', "Путь"),
            'created_at' => Yii::t('app', "Создано"),
            'updated_at' => Yii::t('app', "Обновлено")
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->freeseats == 0 && $this->status == Line::STATUS_WAITING && $this->oldStatus != Line::STATUS_WAITING) {

            $device = Devices::findOne(['user_id' => $this->driver_id]);
            if ($device) {
                $socket = new SocketPusher(['authkey' => $device->auth_token]);
                $socket->push(base64_encode(json_encode([
                    'action' => "acceptDriverTrip",
                    'notifications' => Notifications::create(Notifications::NTD_TRIP_SEATS, [$this->driver_id]),
                    'data' => ['message_id' => time(), 'addressed' => [$this->driver_id], 'line' => $this->toArray(), 'timer' => true]
                ])));
            }

            Queue::processingQueue();

        }
    }

    public function getDriver()
    {
        return Users::findOne($this->driver_id);
    }

    public function getVehicle()
    {
        return \app\modules\api\models\Vehicles::findOne($this->vehicle_id);
    }

    public function getRoute()
    {
        return Route::findOne($this->route_id);
    }

    public function getRouteR()
    {
        return $this->hasOne(Route::class, ['id' => 'route_id']);
    }

    public function getDriverR()
    {
        return $this->hasOne(User::class, ['id' => 'driver_id']);
    }

    public function getStartPoint()
    {
        return Checkpoint::findOne($this->startpoint);
    }

    public function getEndPoint()
    {
        return Checkpoint::findOne($this->endpoint);
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        $action = Yii::$app->controller->action->id;
        if($action != 'path') unset($array['path']);

        if ($this->driver) $array['driver'] = $this->driver->toArray();
        else $array['driver'] = (object)['id' => -1];
        if ($this->vehicle) $array['vehicle'] = $this->vehicle->toArray();
        else $array['vehicle'] = (object)['id' => -1];
        if ($this->route) $array['route'] = $this->route->toArray();
        else $array['route'] = (object)['id' => -1];
        if ($this->startPoint) $array['startpoint'] = $this->startPoint->toArray();
        else $array['startpoint'] = (object)['id' => -1];
        if ($this->endPoint) $array['endpoint'] = $this->endPoint->toArray();
        else $array['endpoint'] = (object)['id' => -1];

        return $array;
    }

}
