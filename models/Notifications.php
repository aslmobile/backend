<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
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
        NT_BLACKLIST        = 1,

        NTP_TRIP_READY      = 2,
        NTP_TRIP_CANCEL     = 3,
        NTP_TRIP_WAIT       = 4,
        NTP_TRIP_FINISHED   = 5,
        NTP_FREE_KM         = 6,
        NTP_TRIP_REVIEW     = 7,
        NTP_TRIP_RATING     = 8,

        NTD_TRIP_SEATS      = 2,
        NTD_TRIP_CANCEL     = 3,
        NTD_TRIP_FINISHED   = 4,
        NTD_TRIP_REVIEW     = 5,
        NTD_TRIP_RATING     = 6,
        NTD_TRIP_QUEUE      = 7,
        NTD_TRIP_READY      = 8,

        NTF_NOTIFICATIONS   = -1,
        NTF_GEO             = -2;

    public static function getTypes()
    {
        return [
            self::NT_DEFAULT    => Yii::t('app', "Стандартная"),
            self::NT_BLACKLIST  => Yii::t('app', "Черный список"),

            self::NTP_TRIP_READY    => Yii::t('app', "Ваша поездка готова"),
            self::NTP_TRIP_CANCEL   => Yii::t('app', "Ваша поездка отменена"),
            self::NTP_TRIP_WAIT     => Yii::t('app', "Ожидание поездки"),
            self::NTP_TRIP_FINISHED => Yii::t('app', "Ваша поездка завершена"),
            self::NTP_FREE_KM       => Yii::t('app', "Бесплатные километры"),
            self::NTP_TRIP_REVIEW   => Yii::t('app', "Отзыв"),
            self::NTP_TRIP_RATING   => Yii::t('app', "Рейтинг"),

            self::NTD_TRIP_SEATS    => Yii::t('app', "Ваша машина заполнена"),
            self::NTD_TRIP_CANCEL   => Yii::t('app', "Ваша поездка отменена"),
            self::NTD_TRIP_FINISHED => Yii::t('app', "Ваша поездка завершена"),
            self::NTD_TRIP_REVIEW   => Yii::t('app', "Отзыв"),
            self::NTD_TRIP_RATING   => Yii::t('app', "Рейтинг"),
            self::NTD_TRIP_QUEUE    => Yii::t('app', "Вы стали в очередь"),
            self::NTD_TRIP_READY    => Yii::t('app', "Ваша поездка готова"),

            self::NTF_NOTIFICATIONS     => Yii::t('app', "Уведомления"),
            self::NTF_GEO               => Yii::t('app', "Геолокация"),
        ];
    }

    const
        STATUS_NEW          = 0,
        STATUS_DELIVERED    = 1,
        STATUS_WAITING      = 2;

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW => Yii::t('app', "Новое уведомление"),
            self::STATUS_DELIVERED => Yii::t('app', "Уведомление доставлено"),
            self::STATUS_WAITING => Yii::t('app', "Уведомление в обработке"),
        ];
    }

    public static function create($type = self::NT_DEFAULT, $user, $important = false, $message = null, $initiator = 0)
    {
        $types = self::getTypes();
        if (!isset ($types[$type]) || !in_array($type, $types)) return false;

        $notification = new Notifications();
        $notification->type = $type;
        $notification->user_id = $user;
        $notification->status = self::STATUS_NEW;
        $notification->title = self::getTypes()[$type];
        $notification->text = $message;
        $notification->initiator_id = $initiator;

        if ($notification->save()) return true;
        return false;
    }
}
