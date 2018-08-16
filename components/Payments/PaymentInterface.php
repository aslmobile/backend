<?php namespace app\components\Payments;

use app\modules\api\models\Users;

interface PaymentInterface
{
    public function addCard(Users $user);
}