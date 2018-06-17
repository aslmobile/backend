<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Translations;

/**
 * TranslationsSearch represents the model behind the search form about `app\modules\admin\models\Translations`.
 */
class TranslationsSearch extends Translations
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['trans_key', 'original_val', 'val', 'descr', 'url'], 'safe'],
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
        $query = Translations::find();

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

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'trans_key', $this->trans_key])
            ->andFilterWhere(['like', 'original_val', $this->original_val])
            ->andFilterWhere(['like', 'val', $this->val])
            ->andFilterWhere(['like', 'descr', $this->descr])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
