<?php

namespace app\models;

use app\components\NotNullBehavior;
use app\components\Push;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "notifications".
 *
 * @property integer $id
 * @property string $title
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $time
 * @property integer $status
 * @property integer $user_id
 * @property integer $type
 * @property integer $todeliver
 * @property string $text
 * @property integer $initiator_id
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notifications';
    }

    public function behaviors()
    {
        return [
            NotNullBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'status', 'user_id', 'type', 'todeliver', 'initiator_id', 'time'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['text'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'title' => Yii::$app->mv->gt('Title', [], 0),
            'created_at' => Yii::$app->mv->gt('Created At', [], 0),
            'updated_at' => Yii::$app->mv->gt('Updated At', [], 0),
            'status' => Yii::$app->mv->gt('Status', [], 0),
            'user_id' => Yii::$app->mv->gt('User ID', [], 0),
            'type' => Yii::$app->mv->gt('Type', [], 0),
            'todeliver' => Yii::$app->mv->gt('Todeliver', [], 0),
            'text' => Yii::$app->mv->gt('Text', [], 0),
            'initiator_id' => Yii::$app->mv->gt('Initiator ID', [], 0),
        ];
    }

    const
        NT_DEFAULT = 0,
        NT_BLACKLIST = 1,
        NT_TRIP_DISBANDED = 33,

        NTP_TRIP_READY = 2,
        NTP_TRIP_CANCEL = 3,
        NTP_TRIP_WAIT = 4,
        NTP_TRIP_FINISHED = 5,
        NTP_FREE_KM = 6,
        NTP_TRIP_REVIEW = 7,
        NTP_TRIP_RATING = 8,
        NTP_TRIP_ARRIVED = 9,
        NTP_TRIP_SCHEDULED = 10,
        NTP_TRIP_QUEUE = 23,

        NTD_TRIP_SEATS = 11,
        NTD_TRIP_CANCEL = 12,
        NTD_TRIP_FINISHED = 13,
        NTD_TRIP_REVIEW = 14,
        NTD_TRIP_RATING = 15,
        NTD_TRIP_QUEUE = 16,
        NTD_TRIP_READY = 17,
        NTD_TRIP_ADD = 18,
        NTD_TRIP_SEAT = 19,
        NTD_TRIP_FIRST = 20,

        NTD_ACCEPTED = 21,
        NTD_VEHICLE_ACCEPTED = 22,

        NTF_NOTIFICATIONS = -1,
        NTF_GEO = -2;

    public static function getTypes()
    {
        return [
            self::NT_DEFAULT => Yii::t('app', "Стандартная"),
            self::NT_BLACKLIST => Yii::t('app', "Черный список"),
            self::NT_TRIP_DISBANDED => Yii::t('app', "Поездка расформирована"),

            self::NTP_TRIP_SCHEDULED => Yii::t('app', "Напоминание о поездке"),
            self::NTP_TRIP_QUEUE => Yii::t('app', "Напоминание о поездке - вы в очереди."),

            self::NTP_TRIP_READY => Yii::t('app', "Машина готова к выезду. Поездка автоматически отменится через 5 минут."),
            self::NTP_TRIP_CANCEL => Yii::t('app', "Поездка отменена"),
            self::NTP_TRIP_WAIT => Yii::t('app', "Машина выехала, ожидайте прибытия."),
            self::NTP_TRIP_FINISHED => Yii::t('app', "Поездка завершена, пожалуйста оцените поездку"),
            self::NTP_TRIP_ARRIVED => Yii::t('app', "Водитель ожидает Вас на остановке. Подтвердите посадку в течении 5 минут."),

            self::NTP_FREE_KM => Yii::t('app', "Бесплатные километры"),
            self::NTP_TRIP_REVIEW => Yii::t('app', "Отзыв"),
            self::NTP_TRIP_RATING => Yii::t('app', "Рейтинг"),

            self::NTD_TRIP_ADD => Yii::t('app', "К вам добавился пассажир"),
            self::NTD_TRIP_SEAT => Yii::t('app', "Посадка в автомобиль"),
            self::NTD_TRIP_SEATS => Yii::t('app', "Ваша машина заполнена. Подтвердите выезд в течении 5 минут."),
            self::NTD_TRIP_CANCEL => Yii::t('app', "Водитель отменил поездку. Вы поедете на ближайшей свободной машине."),
            self::NTD_TRIP_FINISHED => Yii::t('app', "Ваша поездка завершена"),
            self::NTD_TRIP_QUEUE => Yii::t('app', "Вы стали в очередь"),
            self::NTD_TRIP_READY => Yii::t('app', "Ваша поездка готова"),
            self::NTD_TRIP_FIRST => Yii::t('app', "Будьте готовы к выезду. Ваша машина первая в очереди на отправку."),

            self::NTD_TRIP_REVIEW => Yii::t('app', "Отзыв"),
            self::NTD_TRIP_RATING => Yii::t('app', "Рейтинг"),

            self::NTD_ACCEPTED => Yii::t('app', "Вы подтверждены в приложении."),
            self::NTD_VEHICLE_ACCEPTED => Yii::t('app', "Ваша машина подтверждена в приложении."),

            self::NTF_NOTIFICATIONS => Yii::t('app', "Уведомления"),
            self::NTF_GEO => Yii::t('app', "Геолокация"),
        ];
    }

    const
        STATUS_NEW = 0,
        STATUS_DELIVERED = 1,
        STATUS_WAITING = 2,
        STATUS_SCHEDULED = 3;


    public static function getStatuses()
    {
        return [
            self::STATUS_NEW => Yii::t('app', "Новое уведомление"),
            self::STATUS_DELIVERED => Yii::t('app', "Уведомление доставлено"),
            self::STATUS_WAITING => Yii::t('app', "Уведомление в обработке"),
            self::STATUS_SCHEDULED => Yii::t('app', "Запланировано"),
        ];
    }

    public static function create($type = self::NT_DEFAULT, $addressed, $message = '', $initiator = 0, $status = self::STATUS_NEW, $time = null)
    {
        $types = array_keys(self::getTypes());
        if (!isset ($types[$type])) return false;
        $notifications = [];
        if (is_array($addressed)) foreach ($addressed as $user) {
            $notification = new Notifications();
            $notification->type = $type;
            $notification->user_id = $user;
            $notification->status = $status;
            $notification->title = !empty($message) ? $message : self::getTypes()[$type];
            $notification->text = $message;
            $notification->time = $time ? $time : time();
            $notification->initiator_id = $initiator;
            if ($notification->save()) $notifications[] = $notification;
        }
        return $notifications;
    }

    /**
     * @param $notification Notifications
     * @throws
     */
    public static function send($notification)
    {
        /** @var $push Push */
        $push = \Yii::$app->push;
        /*** @var $devices Devices */
        $devices = Devices::find()->where(['user_id' => $notification->user_id])
            ->andWhere(['notifications' => Devices::NOTIFICATION_ENABLED])->all();
        foreach ($devices as $device) {
            switch (intval($device->type)) {
                case Devices::TYPE_IOS:
//                    $push->ios()->send($device->push_id, ['aps' => [
//                        'alert' => $notification->text . ': ' . $notification->title,
//                        'time' => $notification->updated_at,
//                        'sound' => 'default',
//                        'notification_id' => $notification->id,
//                        'type' => $notification->type,
//                    ]]);
                    break;
                case Devices::TYPE_ANDROID:
                    $push->firebase()->send($device->push_id, [
                        'priority' => 'high',
//                        'notification' => [
//                            'title' => $notification->title,
//                            'body' => $notification->text,
//                            'sound' => 'default',
//                        ],
                        'data' => [
                            'title' => $notification->title,
                            'body' => $notification->text,
                            'time' => $notification->time,
                            'type' => $notification->type,
                        ],
                    ], $device->app);
                    break;
            }
        }
        $notification->status = self::STATUS_WAITING;
        $notification->update();
    }
}
