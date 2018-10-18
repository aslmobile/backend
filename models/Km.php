<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "km".
 *
 * @property integer $id
 * @property string $settings
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property array $settings_accumulation
 * @property array $settings_waste
 */
class Km extends \yii\db\ActiveRecord
{

    public $settings_accumulation = false;
    public $settings_waste = false;

    public function afterFind()
    {
        parent::afterFind();

        $settings = json_decode($this->settings, true);
        $this->settings_accumulation = $settings['accumulation'];
        $this->settings_waste = $settings['waste'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'km';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['settings', 'description'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'settings' => Yii::t('app', 'Настрйоки'),
            'description' => Yii::t('app', 'Описание'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
