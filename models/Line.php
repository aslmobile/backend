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
 * @property float $tariff
 * @property int $route_id
 * @property int $startpoint
 * @property int $endpoint
 * @property int $seats
 * @property int $freeseats
 * @property int $starttime
 * @property int $endtime
 * @property int $cancel_reason
 * @property string $angle
 * @property string $position
 * @property string $path
 * @property int $created_at
 * @property int $updated_at
 *
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
                    'route_id',
                    'startpoint',
                    'endpoint',
                    'seats',
                    'freeseats',
                    'cancel_reason',
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
            'route_id' => Yii::t('app', "Маршрут"),
            'startpoint' => Yii::t('app', "Начальная точка"),
            'endpoint' => Yii::t('app', "Конечная точка"),
            'tariff' => Yii::t('app', "Тариф"),
            'status' => Yii::t('app', "Статус"),
            'seats' => Yii::t('app', "Мест"),
            'freeseats' => Yii::t('app', "Свободных мест"),
            'starttime' => Yii::t('app', "Время отправления"),
            'endtime' => Yii::t('app', "Время прибытия"),
            'cancel_reason' => Yii::t('app', "Причина отмены"),
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

        if ($this->freeseats == 0) {

            $watchdog = new RestFul([
                'type' => RestFul::TYPE_DRIVER_ACCEPT,
                'message' => json_encode(['status' => 'request']),
                'user_id' => $this->driver_id,
                'uip' => '0.0.0.0'
            ]);
            $watchdog->save();

            $device = Devices::findOne(['user_id' => $this->driver_id]);
            if ($device) {
                $socket = new SocketPusher(['authkey' => $device->auth_token]);
                $socket->push(base64_encode(json_encode([
                    'action' => "acceptDriverTrip",
                    'data' => ['message_id' => time(), 'addressed' => [$this->driver_id]]
                ])));
            }

            // TODO: Отправка в сокет что машина заполнена и подтверждение о выезде через 5 минут
        }

        if ($this->status == self::STATUS_IN_PROGRESS && $this->getOldAttribute('status') != self::STATUS_IN_PROGRESS) {
            // TODO: Отправлять уведомление всем пассажирам
            Notifications::create(
                Notifications::NTP_TRIP_READY,
                $this->driver_id,
                true,
                \Yii::t('app', "Ваша машина готова к выезду.")
            );
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
}
