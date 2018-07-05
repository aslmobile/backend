<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "region".
 *
 * @property integer $id
 * @property string $title
 * @property int $status
 * @property string $alpha2
 * @property string $alpha3
 * @property int $country_id
 * @property int $created_at
 * @property int $updated_at
 */
class Regions extends \yii\db\ActiveRecord
{
    const
        STATUS_ACTIVE = 1,
        STATUS_DISABLED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region';
    }

    public static function getCountiesList($asArray = false){
        $list = self::find()->asArray($asArray)->all();
        return $asArray ? ArrayHelper::map($list, 'id', 'title_ru') : $list;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'country_id'], 'required'],
            [['title'], 'string', 'max' => 60],
            [['alpha2'], 'string', 'max' => 2],
            [['alpha3'], 'string', 'max' => 3],
            [['status', 'country_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'title' => Yii::t('app', "Название"),
            'alpha2' => Yii::t('app', "ISO Alpha2"),
            'alpha3' => Yii::t('app', "ISO Alpha3"),
            'country_id' => Yii::t('app', "Страна")
        ];
    }
}
