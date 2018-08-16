<?php namespace app\components\paysystem;

use app\models\Transactions;

interface PaymentInterface
{
    public function getForm(Transactions $transaction);
    public function getLink(Transactions $transaction);
    public function updateTransaction();
}