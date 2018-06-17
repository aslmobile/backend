<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "source_message".
 *
 * @property integer $id
 * @property string $category
 * @property string $message
 *
 * @property Message[] $messages
 */
class SourceMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'source_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['category'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'category' => Yii::$app->mv->gt('Категория',[],false),
            'message' => Yii::$app->mv->gt('Сообщение',[],false),
            'source_message_id' => Yii::$app->mv->gt('Source Message',[],false),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['source_message_id' => 'source_message_id']);
    }
}
