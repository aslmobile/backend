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
            [['created_at', 'updated_at', 'status', 'user_id', 'type', 'todeliver', 'initiator_id'], 'integer'],
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

        NTP_TRIP_READY = 2,
        NTP_TRIP_CANCEL = 3,
        NTP_TRIP_WAIT = 4,
        NTP_TRIP_FINISHED = 5,
        NTP_FREE_KM = 6,
        NTP_TRIP_REVIEW = 7,
        NTP_TRIP_RATING = 8,
        NTP_TRIP_ARRIVED = 9,

        NTD_TRIP_SEATS = 10,
        NTD_TRIP_CANCEL = 11,
        NTD_TRIP_FINISHED = 12,
        NTD_TRIP_REVIEW = 13,
        NTD_TRIP_RATING = 14,
        NTD_TRIP_QUEUE = 15,
        NTD_TRIP_READY = 16,
        NTD_TRIP_ADD = 17,
        NTD_TRIP_SEAT = 18,

        NTF_NOTIFICATIONS = -1,
        NTF_GEO = -2;

    public static function getTypes()
    {
        return [
            self::NT_DEFAULT => Yii::t('app', "Стандартная"),
            self::NT_BLACKLIST => Yii::t('app', "Черный список"),

            self::NTP_TRIP_READY => Yii::t('app', "Ваша поездка готова"),
            self::NTP_TRIP_CANCEL => Yii::t('app', "Поездка отменена"),
            self::NTP_TRIP_WAIT => Yii::t('app', "Ожидание поездки"),
            self::NTP_TRIP_FINISHED => Yii::t('app', "Ваша поездка завершена"),
            self::NTP_TRIP_ARRIVED => Yii::t('app', "Прибытие на точку"),

            self::NTP_FREE_KM => Yii::t('app', "Бесплатные километры"),
            self::NTP_TRIP_REVIEW => Yii::t('app', "Отзыв"),
            self::NTP_TRIP_RATING => Yii::t('app', "Рейтинг"),

            self::NTD_TRIP_ADD => Yii::t('app', "К вам добавился пассажир"),
            self::NTD_TRIP_SEAT => Yii::t('app', "К вам сел пассажир"),
            self::NTD_TRIP_SEATS => Yii::t('app', "Ваша машина заполнена"),
            self::NTD_TRIP_CANCEL => Yii::t('app', "Ваша поездка отменена"),
            self::NTD_TRIP_FINISHED => Yii::t('app', "Ваша поездка завершена"),
            self::NTD_TRIP_QUEUE => Yii::t('app', "Вы стали в очередь"),
            self::NTD_TRIP_READY => Yii::t('app', "Ваша поездка готова"),

            self::NTD_TRIP_REVIEW => Yii::t('app', "Отзыв"),
            self::NTD_TRIP_RATING => Yii::t('app', "Рейтинг"),

            self::NTF_NOTIFICATIONS => Yii::t('app', "Уведомления"),
            self::NTF_GEO => Yii::t('app', "Геолокация"),
        ];
    }

    const
        STATUS_NEW = 0,
        STATUS_DELIVERED = 1,
        STATUS_WAITING = 2;

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW => Yii::t('app', "Новое уведомление"),
            self::STATUS_DELIVERED => Yii::t('app', "Уведомление доставлено"),
            self::STATUS_WAITING => Yii::t('app', "Уведомление в обработке"),
        ];
    }

    public static function create($type = self::NT_DEFAULT, $addressed, $message = '', $initiator = 0)
    {
        $types = self::getTypes();
        if (!isset ($types[$type])) return false;
        $notifications = [];
        if (is_array($addressed)) {
            foreach ($addressed as $user) {
                $notification = new Notifications();
                $notification->type = $type;
                $notification->user_id = $user;
                $notification->status = self::STATUS_NEW;
                $notification->title = self::getTypes()[$type];
                $notification->text = $message;
                $notification->initiator_id = $initiator;
                if ($notification->save()) $notifications[] = $notification;
            }
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
        $devices = Devices::find()->where(['user_id' => $notification->user_id])->all();
        foreach ($devices as $device) {
            switch (intval($device->type)) {
                case 1:
//                    $push->ios()->send($device->push_id, ['aps' => [
//                        'alert' => $notification->text . ': ' . $notification->title,
//                        'time' => $notification->updated_at,
//                        'sound' => 'default',
//                        'notification_id' => $notification->id,
//                        'type' => $notification->type,
//                    ]]);
                    break;
                case 2:
                    $push->firebase()->send($device->push_id, [
                        'priority' => 'high',
                        'notification' => [
                            'title' => $notification->title,
                            'body' => $notification->text,
                            'sound' => 'default',
                        ],
                        'data' => [
                            'time' => $notification->updated_at,
                            'notification_id' => $notification->id,
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
