<?php

namespace app\modules\admin\models;

use app\models\Trip;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BotTripSearch represents the model behind the search form about `app\models\BotTrip`.
 */
class BotTripSearch extends BotTrip
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'updated_at', 'status', 'user_id', 'cancel_reason', 'created_by', 'updated_by', 'payment_type', 'startpoint_id', 'route_id', 'seats', 'endpoint_id', 'payment_status', 'vehicle_type_id', 'line_id', 'vehicle_id', 'driver_id', 'need_taxi', 'taxi_status', 'taxi_cancel_reason', 'taxi_time', 'scheduled', 'schedule_id', 'start_time', 'finish_time'], 'integer'],
            [['tariff', 'passenger_rating', 'driver_rating'], 'number'],
            [['created_at', 'passenger_description', 'currency', 'driver_comment', 'luggage_unique_id', 'passenger_comment', 'taxi_address', 'driver_description'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BotTrip::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        $query->andFilterWhere([

            //'id' => $this->id,

            "to_char(date(to_timestamp(created_at)),'dd.mm.yyyy h:ii')" => $this->created_at,

            //'updated_at' => $this->updated_at,

            'status' => $this->status,
            'user_id' => $this->user_id,

            //'amount' => $this->amount,
            //'tariff' => $this->tariff,
            //'cancel_reason' => $this->cancel_reason,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
            //'payment_type' => $this->payment_type,
            //'passenger_rating' => $this->passenger_rating,

            'startpoint_id' => $this->startpoint_id,
            'route_id' => $this->route_id,

            //'seats' => $this->seats,
            //'endpoint_id' => $this->endpoint_id,
            //'payment_status' => $this->payment_status,

            'vehicle_type_id' => $this->vehicle_type_id,
            'line_id' => $this->line_id,

            //'driver_rating' => $this->driver_rating,
            //'vehicle_id' => $this->vehicle_id,
            //'driver_id' => $this->driver_id,
            //'need_taxi' => $this->need_taxi,
            //'taxi_status' => $this->taxi_status,
            //'taxi_cancel_reason' => $this->taxi_cancel_reason,
            //'taxi_time' => $this->taxi_time,
            //'scheduled' => $this->scheduled,
            //'schedule_id' => $this->schedule_id,
            //'start_time' => $this->start_time,
            //'finish_time' => $this->finish_time,

        ]);


//        $query->andFilterWhere(['like', 'passenger_description', $this->passenger_description])
//            ->andFilterWhere(['like', 'currency', $this->currency])
//            ->andFilterWhere(['like', 'driver_comment', $this->driver_comment])
//            ->andFilterWhere(['like', 'luggage_unique_id', $this->luggage_unique_id])
//            ->andFilterWhere(['like', 'passenger_comment', $this->passenger_comment])
//            ->andFilterWhere(['like', 'taxi_address', $this->taxi_address])
//            ->andFilterWhere(['like', 'driver_description', $this->driver_description]);

        return $dataProvider;
    }
}
