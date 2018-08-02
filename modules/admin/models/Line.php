<?php namespace app\modules\admin\models;

use Yii;

/**
 * Vehicles represents the model `\app\models\Line`.
 */
class Line extends \app\models\Line
{
    public function beforeSave($insert)
    {
        $this->starttime = is_numeric($this->starttime) ? $this->starttime : strtotime($this->starttime);
        $this->endtime = is_numeric($this->endtime) ? $this->endtime : strtotime($this->endtime);

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->starttime = is_numeric($this->starttime) ? date("c", $this->starttime) : $this->starttime;
        $this->endtime = is_numeric($this->endtime) ? date("m/d/Y h:i p", $this->endtime) : $this->endtime;

        parent::afterFind();
    }

    public function getVehicle()
    {
        return Vehicles::findOne($this->vehicle_id);
    }

    public function getDriver()
    {
        return User::findOne($this->driver_id);
    }

    public function getRoute()
    {
        return Route::findOne($this->route_id);
    }
}
