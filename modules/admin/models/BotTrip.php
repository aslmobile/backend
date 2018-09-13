<?php

namespace app\modules\admin\models;

use app\models\LuggageType;
use app\models\TripLuggage;
use Yii;


class BotTrip extends \app\models\Trip
{

    public $luggage = [];
    public $_luggages = [];
    public $amount = 0.0;

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['luggage'], 'safe'];
        $rules[] = [['route_id', 'startpoint_id', 'endpoint_id', 'vehicle_type_id', 'seats'], 'required'];
        return $rules;
    }

    public function beforeSave($insert)
    {

        $this->line_id = 0;
        $this->driver_id = 0;
        $this->vehicle_id = 0;

        $route = Route::findOne($this->route_id);
        if (!$route) {
            $this->addError('route_id', Yii::$app->mv->gt("Не найден маршрут", [], false));
            return false;
        };
        $user = User::findOne($this->user_id);
        if (!$user) {
            $this->addError('user_id', Yii::$app->mv->gt("Не найден пользователь", [], false));
            return false;
        };

        $_luggages = [];
        $luggages = $this->luggage;

        TripLuggage::deleteAll(['unique_id' => $this->luggage_unique_id]);

        if (is_array($luggages) && count($luggages) > 0) foreach ($luggages as $luggage) {

            $luggage = LuggageType::findOne($luggage);

            if (empty($luggage)) {
                $this->addError('luggage', Yii::$app->mv->gt("Не найден тип багажа", [], false));
                return false;
            };

            $_luggages[] = $luggage->toArray();

            if ($luggage->need_place) {
                $tariff = $this->calculateLuggageTariff($this->route_id);
                $this->amount = (int)intval($luggage->seats) * (float)floatval($tariff->tariff);
                $this->seats++;
            }

        }

        $luggage_unique = false;
        if ($_luggages && count($_luggages) > 0) {
            foreach ($_luggages as $luggage) $luggage_unique .= $luggage['id'] . '+';
            $luggage_unique .= $user->id . '+' . $route->id;
            $luggage_unique = hash('sha256', md5($luggage_unique) . time());
        }

        if ($luggage_unique) {
            $this->luggage_unique_id = (string)$luggage_unique;
            $this->_luggages = $_luggages;
        }

        return parent::beforeSave($insert);
    }

    public function beforeValidate()
    {
        return parent::beforeValidate();
    }

    public function afterSave($insert, $changedAttributes)
    {

        if ($this->luggage_unique_id) {
            /** @var \app\models\TripLuggage $luggage */
            if ($this->_luggages && count($this->_luggages) > 0) {

                foreach ($this->_luggages as $luggage) {

                    $_luggage = new TripLuggage();
                    $_luggage->unique_id = $this->luggage_unique_id;
                    $_luggage->luggage_type = (int)intval($luggage['id']);
                    $_luggage->amount = (float)floatval($this->amount);
                    $_luggage->status = (int)0;
                    $_luggage->need_place = (int)intval($luggage['need_place']);
                    $_luggage->seats = (int)intval($luggage['seats']);
                    $_luggage->currency = (string)"₸";

                    $_luggage->save(false);

                }

            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        TripLuggage::deleteAll(['unique_id' => $this->luggage_unique_id]);

        $line = \app\models\Line::findOne($this->line_id);
        if(!empty($line)){
            $line->freeseats += $this->seats;
        }
        $line->update();

        parent::afterDelete();
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_CANCELLED => Yii::t('app', "Отменена"),
            self::STATUS_CANCELLED_DRIVER => Yii::t('app', "Отмена водителем"),
            self::STATUS_CREATED => Yii::t('app', "Создана"),
            self::STATUS_WAITING => Yii::t('app', "Ожидает"),
            self::STATUS_WAY => Yii::t('app', "В пути"),
            self::STATUS_FINISHED => Yii::t('app', "Завершена"),
        ];
    }

    protected function calculateLuggageTariff($id)
    {
        $rate = $this->getRate($id);
        $taxi_tariff = 0;

        /** @var \app\models\Route $route */
        $route = Route::find()->where(['id' => $id])->one();
        if (!$route) $this->addError('route_id', Yii::$app->mv->gt("Не найден", [], false));

        $tariff = $route->base_tariff * $rate;

        return (object)[
            'base_tariff' => $route->base_tariff,
            'tariff' => $tariff
        ];
    }

    protected function getRate($route_id)
    {
        /** @var \app\models\Line $line */
        $lines = Line::find()->andWhere([
            'AND',
            ['=', 'route_id', $route_id]
        ])->all();

        if (!$lines) $this->addError('luggage', Yii::$app->mv->gt("Не найден", [], false));

        $seats = 0;
        foreach ($lines as $line) $seats += $line->freeseats;

        $passengers = self::find()->andWhere([
            'AND',
            ['=', 'route_id', $route_id],
            ['=', 'driver_id', 0]
        ])->count();

        if ($seats == 0) $rate = 1.5;
        elseif ($passengers == 0) $rate = 1;
        else {
            $hard_rate = round($passengers / $seats, 2);

            if ($hard_rate <= .35) $rate = 1;
            elseif ($hard_rate >= .35 && $hard_rate <= .6) $rate = 1.1;
            elseif ($hard_rate >= .6 && $hard_rate <= .7) $rate = 1.2;
            elseif ($hard_rate >= .7 && $hard_rate <= .8) $rate = 1.3;
            elseif ($hard_rate >= .8 && $hard_rate <= .9) $rate = 1.4;
            else $rate = 1.5;
        }

        return $rate;
    }

    public function getLuggages()
    {
        return $this->hasMany(TripLuggage::className(), ['unique_id' => 'luggage_unique_id']);
    }

}
