<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "faq".
 *
 * @property int $id
 * @property string $title
 * @property int $status
 * @property int $weight
 * @property string $text
 * @property int $created_at
 * @property int $updated_at
 */
class Faq extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'faq';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'title',
                    'text'
                ],
                'required'
            ],

            [
                [
                    'status',
                    'weight'
                ],
                'integer'
            ],

            [
                [
                    'title',
                    'text'
                ],
                'string'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => Yii::t('app', "ID"),
            'title'                => Yii::t('app', "Заголовок"),
            'text'                => Yii::t('app', "Описание"),
            'weight'                => Yii::t('app', "Сортировка"),
            'status'                => Yii::t('app', "Статус"),
            'created_at'        => Yii::t('app', "Created"),
            'updated_at'        => Yii::t('app', "Updated")
        ];
    }
}
