<?php

namespace app\modules\user\models;


class User extends \app\models\User {

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->generateAuthKey();
            $this->email_confirm_token = md5($this->getAuthKey());
        }
        return parent::beforeSave($insert);
    }
}
