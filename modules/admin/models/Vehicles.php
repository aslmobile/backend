<?php namespace app\modules\admin\models;

use Yii;

/**
 * Vehicles represents the model `\app\models\Vehicles`.
 */
class Vehicles extends \app\models\Vehicles
{
    public function getModelTitle()
    {
        return Yii::t('app', "Автомобиль");
    }

    /**
     * Model Special Content
     * @return string
     */
    public function getSc()
    {
        return 'vehicle';
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_ADDED      => Yii::t('app', "Добавлена"),
            self::STATUS_APPROVED   => Yii::t('app', "Одобрена"),
            self::STATUS_WAITING    => Yii::t('app', "Ждет одобрения")
        ];
    }

    public function getUser()
    {
        return User::findOne(['id' => $this->user_id]);
    }
}
