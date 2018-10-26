<?php


namespace app\components\paysystem;


use app\models\PaymentCards;
use app\models\Transactions;

interface PaysystemSnappingCardsInterface
{
    public function payOutCard(Transactions $transaction, PaymentCards $card);

    public function addCard($user_id);

    public function callbackCard();

    public function deleteCard(PaymentCards $card);

    public function initTransaction(Transactions $transaction, PaymentCards $card);

    public function payTransaction(Transactions $transaction);

    public function updateTransaction();
}
