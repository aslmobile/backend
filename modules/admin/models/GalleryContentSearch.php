<?php

namespace app\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GalleryContentSearch represents the model behind the search form about `app\modules\admin\models\GalleryContent`.
 */
class GalleryContentSearch extends GalleryContent
{

    public $gallery;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_by', 'updated_by', 'status', 'type', 'gallery_id', 'gallery'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
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
        $query = GalleryContent::find()->joinWith('gallery');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $dataProvider->sort->attributes['gallery'] = [
            'asc' => ['gallery.id' => SORT_ASC],
            'desc' => ['gallery.id' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'gallery_content.id' => $this->id,
            'gallery_content.status' => $this->status,
            'gallery_content.type' => $this->type,
            'gallery.id' => $this->gallery,
            "DATE_FORMAT(FROM_UNIXTIME(gallery_content.created_at), '%d.%m.%Y')" => $this->created_at,
            'gallery_content.created_by' => $this->created_by,
            "DATE_FORMAT(FROM_UNIXTIME(gallery_content.updated_at), '%d.%m.%Y')" => $this->updated_at,
            'gallery_content.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'gallery_content.title', $this->title]);

        return $dataProvider;
    }
}
