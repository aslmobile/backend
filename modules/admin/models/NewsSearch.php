<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\News;

/**
 * NewsSearch represents the model behind the search form about `app\models\News`.
 */
class NewsSearch extends News
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['title', 'image', 'description', 'short_description', 'url', 'created_at', 'updated_at'], 'safe'],
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
        $query = News::find();

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

        if(!empty($this->updated_at)){
            $beginOfDay = strtotime("midnight", strtotime($this->updated_at));
            $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;
            $query->andFilterWhere(['>=', 'updated_at', $beginOfDay])
                ->andFilterWhere(['<=', 'updated_at', $endOfDay]);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'short_description', $this->short_description])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
