<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class User
 * @package app\modules\admin\models
 */
class User extends \app\models\User
{
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['password'], 'safe']
        ]);
    }

    public static function getUserListArray($field = 'login'){
        $list = self::find()
            ->select(['id', $field])
            ->where(['status' => self::STATUS_APPROVED])
            ->asArray()
            ->all();
        return ArrayHelper::map($list, 'id', $field);
    }

    public static function getUserRegistrationStatisticData($interval = 7){
        $daysInSeconds = 24 * 60 * 60 * $interval;
        $parsed = [];
        $data = self::findBySql("
            SELECT COUNT(id) AS count, FROM_UNIXTIME(created_at, '%Y-%m-%d') AS date
            FROM " . self::tableName() . "
              WHERE created_at >= UNIX_TIMESTAMP() - :interval
            GROUP BY FROM_UNIXTIME(created_at, '%Y-%m-%d')
            ORDER BY date ASC ;
        ", [':interval' => $daysInSeconds])->asArray()->all();
        foreach ($data as $datum) {
            $parsed[$datum['date']] = $datum['count'];
        }
        return $parsed;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->generateAuthKey();
            $this->email_confirm_token = md5($this->getAuthKey());
        }

        if (parent::beforeSave($insert)) {
            if ($insert) {
                if (!empty($this->password)) {
                    $this->setPassword($this->password);
                } else {
                    $this->addError('password', Yii::$app->mv->gt('Это поле обязатльоне для заполнения!', [], false));
                    return false;
                }
            } elseif (!empty($this->password)) {
                $this->setPassword($this->password);
            }
            return true;
        }
        return false;
    }

    public function beforeValidate()
    {
        if (isset ($this->status) && $this->status != $this::STATUS_BLOCKED && $this->getOldAttribute('status') == $this::STATUS_BLOCKED) {
            $this->blocked_reason = null;
            $this->blocked_at = null;
            $this->blocked_by = null;
        }

        if (!empty($this->blocked_reason)) {
            $this->blocked_at = time();
            $this->blocked_by = Yii::$app->user->getId();
        }
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'password' => Yii::$app->mv->gt('Пароль', [], false)
        ]);
    }

    public function getRoles()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])
            ->viaTable('auth_assignment', ['user_id' => 'id']);
    }

    public function getTransactionsDataProvider()
    {
        return $this->transactionsSearchModel ? $this->transactionsSearchModel->search(Yii::$app->request->queryParams, $this->id) : false;
    }

    public function getTransactionsSearchModel()
    {
        return new TransactionSearch();
    }
}
