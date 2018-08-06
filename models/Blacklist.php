<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
/**
 * This is the model class for table "blacklist".
 *
 * @property integer $id
 * @property string $add_comment
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $user_id
 * @property integer $add_type
 * @property string $description
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $cancel_comment
 */
class Blacklist extends \yii\db\ActiveRecord
{
    const
        STATUS_BLACKLISTED = 1,
        STATUS_DISBAND = 0;

    const
        TYPE_AUTO = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blacklist';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'status', 'user_id', 'add_type', 'created_by', 'updated_by'], 'integer'],
            [['add_comment', 'cancel_comment'], 'string', 'max' => 1000],
            [['description'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::$app->mv->gt('ID', [], 0),
            'add_comment' => Yii::$app->mv->gt('Комментарий', [], 0),
            'status' => Yii::$app->mv->gt('Статус', [], 0),
            'user_id' => Yii::$app->mv->gt('Пользователь', [], 0),
            'add_type' => Yii::$app->mv->gt('Тип блокировки', [], 0),
            'description' => Yii::$app->mv->gt('Описание', [], 0),
            'created_at' => Yii::$app->mv->gt('Добавлен', [], 0),
            'updated_at' => Yii::$app->mv->gt('Обновлен', [], 0),
            'cancel_comment' => Yii::$app->mv->gt('Комментарий отмены', [], 0),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getUser()
    {
        return \app\modules\admin\models\User::findOne($this->user_id);
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_BLACKLISTED => Yii::t('app', "Заблокирован"),
            self::STATUS_DISBAND => Yii::t('app', "Разблокирован")
        ];
    }
}
