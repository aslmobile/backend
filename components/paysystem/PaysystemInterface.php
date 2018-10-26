<?php

namespace app\components\paysystem;

use app\models\PaymentCards;
use app\models\Transactions;


interface PaysystemInterface
{
    public function getLink(Transactions $transaction);

    public function payOut(Transactions $transaction, PaymentCards $card);

    public function updateTransaction();
}
