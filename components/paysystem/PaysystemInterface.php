<?php

namespace app\components\paysystem;
use app\models\Transactions;


/**
 * Created by PhpStorm.
 * User: Graf
 * Date: 29.03.2017
 * Time: 10:53
 */
interface PaysystemInterface
{
    public function getForm(Transactions $transaction);
    public function getLink(Transactions $transaction);
    public function updateTransaction();
}