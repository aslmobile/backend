<?php


namespace app\components\paysystem\Drivers;


use app\components\paysystem\PaysystemSnappingCardsInterface;
use app\models\PaymentCards;
use app\models\TransactionLog;
use app\models\Transactions;
use app\modules\api\Module;
use SimpleXMLElement;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class PayBoxSnappingCards implements PaysystemSnappingCardsInterface
{
    //Paysystem url
    private $addUrl = "https://paybox.kz/v1/merchant/{merchant_id}/cardstorage/add";
    private $listUrl = "https://paybox.kz/v1/merchant/{merchant_id}/cardstorage/list";
    private $deleteUrl = "https://paybox.kz/v1/merchant/{merchant_id}/cardstorage/remove";
    private $initUrl = "https://paybox.kz/v1/merchant/{merchant_id}/card/init";
    private $payUrl = "https://paybox.kz/v1/merchant/{merchant_id}/card/pay";

    // driver settings
    private $driver = 'PayBoxSnappingCards';
    private $key = '';
    private $merchant_id = '';
    private $devMod = true;
    private $currency = 'KZT';
    public $result = 0;

    /**
     * PayBoxSnappingCards constructor.
     * @param bool $receipt
     */
    public function __construct($receipt = true)
    {
        if ($receipt) {
            $this->key = \Yii::$app->params['paysystem'][$this->driver]['secret_key_receipt'];
        } else {
            $this->key = \Yii::$app->params['paysystem'][$this->driver]['secret_key_pay'];
        }
        $this->merchant_id = \Yii::$app->params['paysystem'][$this->driver]['merchant_id'];
        $this->devMod = \Yii::$app->params['paysystem'][$this->driver]['dev'];
        if (isset(\Yii::$app->params['paysystem'][$this->driver]['currency'])) {
            $this->currency = \Yii::$app->params['paysystem'][$this->driver]['currency'];
        }
        $this->addUrl = str_replace('{merchant_id}', $this->merchant_id, $this->addUrl);
        $this->listUrl = str_replace('{merchant_id}', $this->merchant_id, $this->listUrl);
        $this->deleteUrl = str_replace('{merchant_id}', $this->merchant_id, $this->deleteUrl);
    }

    /**
     * @param $user_id
     * @return Transactions|null
     * @throws \Exception
     */
    public function addCard($user_id)
    {
        if (!$transaction = Transactions::findOne([
            'user_id' => $user_id,
            'type' => Transactions::TYPE_OUTCOME,
            'status' => Transactions::STATUS_REQUEST,
            'gateway' => Transactions::GATEWAY_PAYBOX,

        ])) {
            $transaction = new Transactions([
                'user_id' => $user_id,
                'type' => Transactions::TYPE_OUTCOME,
                'status' => Transactions::STATUS_REQUEST,
                'gateway' => Transactions::GATEWAY_PAYBOX,
                'currency' => $this->currency,
                'uip' => Yii::$app->request->userIP,
                'amount' => 0,
                'route_id' => 0,
            ]);
            $transaction->save();
        }

        $transaction_log = new TransactionLog([
            'transaction_id' => $transaction->id,
            'driver' => $this->driver,
            'action' => 'addCard'
        ]);

        $data = [
            'pg_merchant_id' => $this->merchant_id,
            'pg_user_id' => $user_id,
            'pg_order_id' => $transaction->id,
            'pg_post_link' => Url::toRoute(['/main/payment/callback', 'driver' => $this->driver], true),
            'pg_back_link' => Url::toRoute(['/main/payment/success'], true),
            'pg_check_url' => Url::toRoute(['/main/payment/check', 'driver' => $this->driver], true),
            'pg_result_url' => Url::toRoute(['/main/payment/result', 'driver' => $this->driver], true),
            'pg_success_url' => Url::toRoute(['/main/payment/success'], true),
            'pg_failure_url' => Url::toRoute(['/main/payment/fail'], true),
            'pg_testing_mode' => intval($this->devMod),
            'pg_salt' => substr(md5(time()), 0, 16),
        ];

        $transaction_log->request = json_encode($data);
        $transaction->request = json_encode($data);

        $response = $this->sendRequest($data, $this->addUrl);

        $transaction_log->response = json_encode($response);
        $transaction->response = json_encode($response);
        $transaction_log->save();

        if (isset($response['error'])) {
            $transaction_log->error_code = $response['error'];
            $transaction_log->error_message = $response['message'];

            $transaction_log->save();
        } elseif ($this->checkSign($response, $this->addUrl)) {
            if (isset($response['pg_payment_id'])) {
                $transaction->payment_id = $response['pg_payment_id'];
            }
            if (isset($response['pg_redirect_url'])) {
                $transaction->payment_link = $response['pg_redirect_url'];
            }
        }

        $transaction->save();

        return $transaction;
    }

    /**
     * @return array
     */
    public function callbackCard()
    {
        $log = new TransactionLog([
            'driver' => $this->driver,
        ]);
        \Yii::$app->response->format = Response::FORMAT_XML;

        $data = Yii::$app->request->post();
        $log->request = json_encode($data);

        if ($data) {
            $log->action = 'callbackCard';
            //$response = $this->checkData($data, $log);
        }

        if ($data['pg_status'] == 'error') {
            $log->error_code = 1;
            $log->error_message = $data['pg_description'];
        }

        $log->response = json_encode([]);
        $log->save();

        return [];
    }

    /**
     * @param $user_id
     * @return array
     * @throws \Exception
     */
    public function getCardsList($user_id)
    {
        $transaction_log = new TransactionLog([
            'transaction_id' => 0,
            'driver' => $this->driver,
            'action' => 'getCardsList'
        ]);

        $data = [
            'pg_merchant_id' => $this->merchant_id,
            'pg_user_id' => $user_id,
            'pg_salt' => substr(md5(time()), 0, 16),
        ];

        $list = [];

        $transaction_log->request = json_encode($data);

        $response = $this->sendRequest($data, $this->listUrl);

        $transaction_log->response = json_encode($response);
        $transaction_log->save();

        if (isset($response['error'])) {

            $transaction_log->error_code = $response['error'];
            $transaction_log->error_message = $response['message'];
            $transaction_log->save();

        } elseif ($this->checkSign($response, $this->listUrl)) {

            $list_ids = [];

            if (isset($response['card']) && is_array($response['card'])) {
                if (!key_exists('pg_card_id', $response['card'])) {
                    foreach ($response['card'] as $card) {
                        $list_ids[] = $card['pg_card_id'];
                        $this->updateCard($card, $user_id);
                    }
                } else {
                    $list_ids[] = $response['card']['pg_card_id'];
                    $this->updateCard($response['card'], $user_id);
                }
            }

            if (!PaymentCards::find()->where(['user_id' => $user_id, 'status' => PaymentCards::STATUS_MAIN])->count()) {
                if ($t_card = PaymentCards::findOne(['user_id' => $user_id])) {
                    $t_card->status = PaymentCards::STATUS_MAIN;
                    $t_card->save();
                }
            }

            if (!empty($list_ids)) {
                PaymentCards::deleteAll(['AND', ['=', 'user_id', $user_id], ['NOT IN', 'pg_card_id', $list_ids]]);
            } else {
                PaymentCards::deleteAll(['user_id' => $user_id]);
            }
        }

        return $list;
    }

    /**
     * @param PaymentCards $card
     * @return PaymentCards
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteCard(PaymentCards $card)
    {
        $transaction_log = new TransactionLog([
            'transaction_id' => 0,
            'driver' => $this->driver,
            'action' => 'deleteCard'
        ]);

        $data = [
            'pg_merchant_id' => $this->merchant_id,
            'pg_user_id' => $card->user_id,
            'pg_card_id' => $card->pg_card_id,
            'pg_salt' => substr(md5(time()), 0, 16),
        ];

        $transaction_log->request = json_encode($data);

        $response = $this->sendRequest($data, $this->deleteUrl);

        $transaction_log->response = json_encode($response);
        $transaction_log->save();

        $card_response = isset($response['card']) ? $response['card'] : [];

        if (isset($response['error'])) {
            $transaction_log->error_code = $response['error'];
            $transaction_log->error_message = $response['message'];
            $transaction_log->save();
        } elseif (isset($card_response['pg_status']) && $card_response['pg_status'] == 'error') {

            $error_description = isset($card_response['pg_error_description']) ?
                $card_response['pg_error_description'] :
                isset($response['pg_error_description']) ? $response['pg_error_description'] : '';

            $transaction_log->error_code = 1;
            $transaction_log->error_message = $error_description;
            $transaction_log->save();

        } elseif ($this->checkSign($response, $this->deleteUrl)) {
            $card->delete();
            if (
                $card->oldAttributes['status'] == PaymentCards::STATUS_MAIN
                ||
                !PaymentCards::find()->where(['user_id' => $card->user_id, 'status' => PaymentCards::STATUS_MAIN])->count()
            ) {
                $t_card = PaymentCards::findOne(['user_id' => $card->user_id]);
                if (!empty($t_card)) {
                    $t_card->status = PaymentCards::STATUS_MAIN;
                    $t_card->save();
                }
            }
        }

        if ($transaction_log->error_code) {
            /** @var Module $module */
            $module = Yii::$app->module;
            $module->setError(422, '_card', $transaction_log->error_message);
        }

        return $card;
    }

    /**
     * @param Transactions $transaction
     * @param PaymentCards $card
     * @return Transactions|bool
     * @throws \Exception
     */
    public function initTransaction(Transactions $transaction, PaymentCards $card)
    {
        $transaction_log = new TransactionLog([
            'transaction_id' => $transaction->id,
            'driver' => $this->driver,
            'action' => 'initTransaction'
        ]);

        $data = [
            'pg_merchant_id' => $this->merchant_id,
            'pg_order_id' => $transaction->id,
            'pg_user_id' => $transaction->user_id,
            'pg_card_id' => $card->id,
            'pg_amount' => $transaction->amount,
            'pg_currency' => $this->currency,
            'pg_check_url' => Url::toRoute(['/main/payment/check', 'driver' => $this->driver], true),
            'pg_result_url' => Url::toRoute(['/main/payment/result', 'driver' => $this->driver], true),
            'pg_success_url' => Url::toRoute(['/main/payment/success'], true),
            'pg_failure_url' => Url::toRoute(['/main/payment/fail'], true),
            'pg_testing_mode' => intval($this->devMod),
            'pg_salt' => substr(md5(time()), 0, 16),
            'pg_description' => 'test',
            'pg_sig' => '',
        ];

        if ($transaction->route_id) {
            $data['pg_description'] = "Оплата услуг на маршруте № {$transaction->route_id}";
        } else {
            $data['pg_description'] = "Пополнение баланса";
        }

        $transaction_log->request = json_encode($data);

        $response = $this->sendRequest($data, $this->initUrl);

        $transaction_log->response = json_encode($response);
        $transaction_log->save();

        if (isset($response['error'])) {

            $transaction_log->error_code = $response['error'];
            $transaction_log->error_message = $response['message'];
            $transaction_log->save();

            $transaction->status = Transactions::STATUS_REJECTED;
            $transaction->save();

            return false;

        } elseif ($this->checkSign($response, $this->listUrl)) {
            if (isset($response['pg_payment_id'])) {
                $transaction->payment_id = $response['pg_payment_id'];
                $transaction->save();
            }
        }

        return $transaction;
    }

    /**
     * @param Transactions $transaction
     * @return Transactions|bool
     * @throws \Exception
     */
    public function payTransaction(Transactions $transaction)
    {
        $transaction_log = new TransactionLog([
            'transaction_id' => $transaction->id,
            'driver' => $this->driver,
            'action' => 'payTransaction'
        ]);

        $data = [
            'pg_merchant_id' => $this->merchant_id,
            'pg_payment_id' => $transaction->payment_id,
            'pg_salt' => substr(md5(time()), 0, 16),
            'pg_sig' => '',
        ];

        $transaction_log->request = json_encode($data);

        $response = $this->sendRequest($data, $this->payUrl);

        var_dump($response);die();

        $transaction_log->response = json_encode($response);
        $transaction_log->save();

        if (isset($response['error'])) {

            $transaction->status = Transactions::STATUS_REJECTED;

            $transaction_log->error_code = $response['error'];
            $transaction_log->error_message = $response['message'];
            $transaction_log->save();

            return false;

        } elseif ($this->checkSign($response, $this->listUrl)) {
            $transaction->status = Transactions::STATUS_PAID;
            $transaction->save();
        }

        return $transaction;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function updateTransaction()
    {
        $log = new TransactionLog([
            'driver' => $this->driver,
        ]);
        \Yii::$app->response->format = Response::FORMAT_XML;

        $data = Yii::$app->request->post();
        $log->request = json_encode($data);

        if ($this->result) {
            $log->action = 'checkTransaction';
            $response = $this->checkData($data, $log);
        } else {
            $log->action = 'updateTransaction';
            $response = $this->updateData($data, $log);
        }

        if ($response['pg_status'] == 'rejected') {
            $log->error_code = 1;
            $log->error_message = $response['pg_description'];
        }

        $log->response = json_encode($response);
        $log->save();

        return $response;
    }

    /**
     * @param $data
     * @param TransactionLog $log
     * @return array
     * @throws \Exception
     */
    private function updateData($data, TransactionLog &$log)
    {
        $url = (isset($_SERVER['REQUEST_URI'])) ?
            $_SERVER['REQUEST_URI'] :
            Url::toRoute(['/main/payment/check', 'driver' => $this->driver], true);
        $response = [
            'pg_salt' => substr(md5(time()), 0, 16),
            'pg_status' => 'rejected',
            'pg_sig' => '',
        ];

        if ($data['pg_sig'] == $this->getSignature((array)$data, $url)) {
            $data = (array)$data;

            $order_id = (isset($data['pg_order_id']) && !empty($data['pg_order_id'])) ? $data['pg_order_id'] : false;
            $payment_id = (isset($data['pg_payment_id']) && !empty($data['pg_payment_id'])) ? $data['pg_payment_id'] : false;

            if ($order_id || $payment_id) {
                if ($order_id) {
                    $transaction = Transactions::findOne(intval($order_id));
                } else {
                    $transaction = Transactions::find()->where(['=', 'payment_id', intval($payment_id)])->one();
                }

                if (!empty($transaction)) {
                    $log->transaction_id = $transaction->id;
                    if ($transaction->amount == $data['pg_amount'] && $data['pg_currency'] == $this->currency) {
                        if ($data['pg_result']) {
                            $transaction->status = Transactions::STATUS_PAID;
                            $response['pg_status'] = 'ok';
                        } else {
                            $transaction->status = Transactions::STATUS_REJECTED;
                            $response['pg_description'] = $data['pg_failure_description'];
                        }
                        $transaction->save(false);
                    } else {
                        $response['pg_description'] = Yii::$app->mv->gt('Не верная валюта или сумма', [], false);
                    }
                } else {
                    $response['pg_description'] = Yii::$app->mv->gt('Транзакция не найдена', [], false);
                }
            } else {
                $response['pg_description'] = Yii::$app->mv->gt('pg_order_id обязательно для заполнения', [], false);
            }
        } else {
            $response['pg_description'] = Yii::$app->mv->gt('Не верная подпись запроса', [], false);
        }

        $response['pg_sig'] = $this->getSignature($response, $url);

        return $response;
    }

    /**
     * @param $data
     * @param TransactionLog $log
     * @return array
     * @throws \Exception
     */
    private function checkData($data, TransactionLog &$log)
    {
        $url = (isset($_SERVER['REQUEST_URI'])) ?
            $_SERVER['REQUEST_URI'] :
            Url::toRoute(['/main/payment/check', 'driver' => $this->driver], true);
        $response = [
            'pg_salt' => substr(md5(time()), 0, 16),
            'pg_status' => 'rejected',
            'pg_sig' => '',
        ];
        if ($data['pg_sig'] == $this->getSignature((array)$data, $url)) {
            $data = (array)$data;

            $order_id = (isset($data['pg_order_id']) && !empty($data['pg_order_id'])) ? $data['pg_order_id'] : false;
            $payment_id = (isset($data['pg_payment_id']) && !empty($data['pg_payment_id'])) ? $data['pg_payment_id'] : false;

            if ($order_id || $payment_id) {
                if ($order_id) {
                    $transaction = Transactions::findOne(intval($order_id));
                } else {
                    $transaction = Transactions::find()->where(['=', 'payment_id', intval($payment_id)])->one();
                }
                if (!empty($transaction)) {
                    $log->transaction_id = $transaction->id;
                    if ($transaction->amount == $data['pg_amount'] && $data['pg_currency'] == $this->currency) {
                        $transaction->status = Transactions::STATUS_PAID;
                        $transaction->save(false);
                        $response['pg_status'] = 'ok';
                    } else {
                        $response['pg_description'] = Yii::$app->mv->gt('Не верная валюта или сумма', [], false);
                    }
                } else {
                    $response['pg_description'] = Yii::$app->mv->gt('Транзакция не найдена', [], false);
                }
            } else {
                $response['pg_description'] = Yii::$app->mv->gt('pg_order_id обязательно для заполнения', [], false);
            }
        } else {
            $response['pg_description'] = Yii::$app->mv->gt('Не верная подпись запроса', [], false);
        }

        $response['pg_sig'] = $this->getSignature($response, $url);

        return $response;
    }

    /**
     * @param $data
     * @param $user_id
     */
    private function updateCard($data, $user_id)
    {
        $card = PaymentCards::findOne(['pg_card_id' => $data['pg_card_id'], 'user_id' => $user_id]);

        if (!$card) {
            $card = new PaymentCards([
                'user_id' => $user_id,
                'pg_card_id' => (isset($data['pg_card_id'])) ? $data['pg_card_id'] : '',
                'pg_card_hash' => (isset($data['pg_card_hash'])) ? $data['pg_card_hash'] : '',
                'pg_merchant_id' => (isset($data['pg_merchant_id'])) ? $data['pg_merchant_id'] : '',
                'status' => (PaymentCards::find()->where(['user_id' => $user_id, 'status' => PaymentCards::STATUS_MAIN])->count()) ?
                    PaymentCards::STATUS_ACTIVE :
                    PaymentCards::STATUS_MAIN,
            ]);
        }

        $card->save();
    }

    /**
     * @param array $data
     * @param $url
     * @return array|mixed
     * @throws \Exception
     */
    private function sendRequest(array $data, $url)
    {
        $data['pg_sig'] = $this->getSignature($data, $url);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response = self::xmlToArray($response)) {
            $response = ['error' => 500, 'message' => 'Paysystem return invalid response!'];
        }

        return $response;
    }

    /**
     * Create XML
     *
     * @param array $data
     * @param SimpleXMLElement $xml_data
     */
    public static function toXML($data, SimpleXMLElement &$xml_data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item' . $key; //dealing with <0/>..<n/> issues
            }
            if (is_array($value)) {
                $subnode = $xml_data->addChild($key);
                self::toXML($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    /**
     * @param $data
     * @return array|mixed
     */
    public static function xmlToArray($data)
    {
        try {
            $data = simplexml_load_string($data);
            return json_decode(json_encode($data), 1);
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * Method create signature
     *
     * @param array $data
     * @param string $url
     * @return string
     * @throws \Exception
     */
    private function getSignature(array $data, $url)
    {
        preg_match_all('/\/([\w.-]+)[?]?/m', $url, $matches);


        if (empty($matches)) {
            throw new \Exception(Yii::$app->mv->gt('Платежная система: Не верный платежный метод', [], false));
        } else {
            $action = array_pop($matches[1]);
        }

        unset($data['pg_sig']);
        ksort($data, SORT_STRING);
        array_unshift($data, $action);
        array_push($data, $this->key);
        $signString = self::arrayImplodeData($data, ';');

        return md5($signString);
    }

    /**
     * @param $array
     * @param string $glue
     * @return string
     */
    private static function arrayImplodeData($array, $glue = ';')
    {
        $res = '';
        $s = '';

        if (is_array($array)) {
            foreach ($array as $value) {
                if (is_array($value)) {
                    ksort($value, SORT_STRING);
                    $r = self::arrayImplodeData($value, $glue);
                } else {
                    $r = $value;
                }

                $res .= $s . $r;
                $s = $glue;
            }
        }

        return $res;
    }

    /**
     * @param $data
     * @param $url
     * @return bool
     * @throws \Exception
     */
    private function checkSign($data, $url)
    {
        $sign = $this->getSignature($data, $url);

        if (isset($data['pg_sig'])) {
            echo __FILE__ . ':' . __LINE__ . ' <pre>';
            print_r([$sign, $data['pg_sig']]);
            echo '</pre>';
            if (isset($data['pg_sig']) && $data['pg_sig'] == $sign) {
                return true;
            }
        }

        return false;
    }
}
