<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductsSearch represents the model behind the search form about `app\modules\admin\models\Products`.
 */
class ProductsSearch extends Products
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'status'], 'integer'],
            [['title', 'short_description', 'full_description', 'small_image', 'image', 'created_at'], 'safe'],
            [['price'], 'number'],
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
        $query = Products::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pageSize' => 50,
			],
			'sort'=> ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
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
            'price' => $this->price,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
