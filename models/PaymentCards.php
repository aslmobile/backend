<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "payment_cards".
 *
 * @property integer $id
 * @property integer $status
 * @property string $pg_card_id
 * @property string $pg_card_hash
 * @property integer $pg_merchant_id
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class PaymentCards extends \yii\db\ActiveRecord
{
    const
        STATUS_DISABLED = 0,
        STATUS_ACTIVE = 1,
        STATUS_MAIN = 2,
        STATUS_DELETED = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_cards';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'pg_merchant_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['pg_card_id', 'pg_card_hash'], 'string', 'max' => 255],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pg_card_id' => Yii::$app->mv->gt('ID карты', [], 0),
            'pg_card_hash' => Yii::$app->mv->gt('Хеш карты', [], 0),
            'pg_merchant_id' => Yii::$app->mv->gt('ID Магазина', [], 0),
            'status' => Yii::$app->mv->gt('Статус', [], 0),
            'user_id' => Yii::$app->mv->gt('Пользователь', [], 0),
            'created_at' => Yii::$app->mv->gt('Добавлена', [], 0),
            'updated_at' => Yii::$app->mv->gt('Обновлена', [], 0)
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_DISABLED => Yii::t('app', "Не активная"),
            self::STATUS_ACTIVE => Yii::t('app', "Активная"),
            self::STATUS_MAIN => Yii::t('app', "Основная"),
            self::STATUS_DELETED => Yii::t('app', "Удалена")
        ];
    }

    public function delete()
    {
        $this->status = self::STATUS_DELETED;
        return $this->save();
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);

        $array['mask'] = $this->getCardMask();

        return $array;
    }

    public function getCardMask()
    {
        $card_number_f4 = substr($this->pg_card_hash, 0, 4);
        $card_number_l4 = substr($this->pg_card_hash, 15, 4);

        return $card_number_f4 . ' **** **** ' . $card_number_l4;
    }

    public static function getCards($user_id)
    {
        $cards = self::find()->where(['user_id' => $user_id, 'status' => [self::STATUS_ACTIVE, self::STATUS_MAIN]])->all();

        $cards_list = [];
        if (!empty($cards)) foreach ($cards as $card) $cards_list[] = $card->toArray();

        return $cards_list;
    }
}
