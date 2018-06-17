<?php namespace app\modules\api\models;

/**
 * @property \app\modules\api\models\Users $user
 */
class Devices extends \app\models\Devices
{
    /**
     * @return \app\models\User
     */
    public function getUser()
    {
        return Users::findOne(['id' => $this->user_id]);
    }
}