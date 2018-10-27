<?php

namespace app\components\paysystem;

use app\models\Transactions;
use app\models\User;


interface PaysystemInterface
{
    public function getForm(Transactions $transaction);

    public function payOut(Transactions $transaction, User $user);

    public function getLink(Transactions $transaction);

    public function updateTransaction();
}
