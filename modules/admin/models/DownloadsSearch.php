<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Downloads;

/**
 * DownloadsSearch represents the model behind the search form about `app\modules\admin\models\Downloads`.
 */
class DownloadsSearch extends Downloads
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'status'], 'integer'],
            [['title', 'source', 'created_at'], 'safe'],
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
        $query = Downloads::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pageSize' => 50,
			],
			'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!empty($this->created_at)){
            $beginOfDay = strtotime("midnight", strtotime($this->created_at));
            $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;
            $query->andFilterWhere(['>=', 'created_at', $beginOfDay])
                ->andFilterWhere(['<=', 'created_at', $endOfDay]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'source', $this->source]);

        return $dataProvider;
    }
}
