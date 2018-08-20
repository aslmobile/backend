<?php namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use app\components\MultilingualBehavior;
use app\components\MultilingualQuery;
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
            self::STATUS_DISABLED   => Yii::t('app', "Не активная"),
            self::STATUS_ACTIVE     => Yii::t('app', "Активная"),
            self::STATUS_DELETED    => Yii::t('app', "Удалена")
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

        // XXXX-XXXX-XXXX-XXXX
        $chash = $array['pg_card_hash'];
        $card = substr($chash, 0, 4);
        $card = $card . ' XXXX XXXX ' . substr($chash, 15, 4);

        $array['mask'] = $card;
        return $array;
    }

    public static function getCards($user_id)
    {
        $cards = self::find()->andWhere([
            'AND',
            ['=', 'user_id', $user_id],
            ['=', 'status', self::STATUS_ACTIVE]
        ])->all();

        $cards_list = [];
        if ($cards && count($cards) > 0) foreach ($cards as $card) $cards_list[] = $card->toArray();

        return $cards_list;
    }
}
