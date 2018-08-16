<?php
namespace app\components\paysystem\Drivers;

use app\components\paysystem\PaymentInterface;
use app\models\Log;
use app\models\Paysystem;
use app\models\TransactionLog;
use app\models\Transactions;
use app\models\UserSeminar;
use SimpleXMLElement;
use Yii;
use yii\db\Transaction;
use yii\helpers\Url;
use yii\web\Response;

class PayBox  implements PaymentInterface
{
    // Pay system settings
    private $actionUrl = 'https://www.paybox.kz/init_payment.php';

    // driver settings
    private $driver = 'PayBox';
    private $template = '@app/components/paysystem/views/paybox';
    private $key = '';
    private $merchant_id = '';
    private $devMod = true;
    private $currency = 'KZT';
    public $result = 0;

    public function __construct($receipt = true) {
        if($receipt){
            $this->key = \Yii::$app->params['paysystem'][$this->driver]['secret_key_receipt'];
        }else{
            $this->key = \Yii::$app->params['paysystem'][$this->driver]['secret_key_pay'];
        }
        $this->merchant_id = \Yii::$app->params['paysystem'][$this->driver]['merchant_id'];
        $this->devMod = \Yii::$app->params['paysystem'][$this->driver]['dev'];
        if(isset(\Yii::$app->params['paysystem'][$this->driver]['currency'])){
            $this->currency = \Yii::$app->params['paysystem'][$this->driver]['currency'];
        }
    }

    public function getForm(Transactions $transaction)
    {
        // TODO: Implement getForm() method.
    }

    public function getLink(Transactions $transaction) {
        $log = new TransactionLog([
            'transaction_id' => $transaction->id,
            'driver' => $this->driver,
            'action' => 'getLink'
        ]);
        $url = $this->actionUrl;
        $data = [
            'pg_merchant_id' => $this->merchant_id,
            'pg_order_id' => $transaction->id,
            'pg_amount' => $transaction->amount,
            'pg_currency' => $this->currency,
            'pg_check_url' => Url::toRoute(['/main/payment/check', 'driver' => $this->driver], true),
            'pg_result_url' => Url::toRoute(['/main/payment/result', 'driver' => $this->driver], true),
            'pg_success_url' => Url::toRoute(['/main/payment/success'], true),
            'pg_failure_url' => Url::toRoute(['/main/payment/fail'], true),
            'pg_testing_mode' => intval($this->devMod),
            'pg_salt' => substr(md5(time()),0,16),
            'pg_description' => 'test',
            'pg_sig' => '',
        ];

        $data['pg_sig'] = $this->getSignature($data, $url);

        $log->request = json_encode($data);
        $transaction->request = json_encode($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        $response = curl_exec($ch);
        curl_close($ch);

        $log->response = $response;
        $transaction->response = $response;

        if($response = simplexml_load_string($response)){
            if($response->pg_sig == $this->getSignature((array)$response, $url)){
                $response = (array)$response;
                if(isset($response['pg_status']) && !empty($response['pg_status'])){
                    switch ($response['pg_status']){
                        case 'ok':
                            $transaction->paysystem_id = $response['pg_payment_id'];
                            $transaction->paysystem_link = $response['pg_redirect_url'];
                            break;
                        case 'rejected':
                        case 'error':
                            $log->error_code = 1;
                            $log->error_message = $response['pg_description'];
                            break;
                    }
                }else{
                    $log->error_code = 1;
                    $log->error_message = 'Invalid server response';
                }
            }else{
                $log->error_code = 1;
                $log->error_message = 'Invalid signature';
            }
        }

        $transaction->save();
        $log->save();

        return $transaction;
    }

    public function updateTransaction(){
        $log = new TransactionLog([
            'driver' => $this->driver,
        ]);
        \Yii::$app->response->format = Response::FORMAT_XML;

        $data = Yii::$app->request->post();
        $log->request = json_encode($data);

        if($this->result){
            $log->action = 'checkTransaction';
            $response = $this->checkData($data, $log);
        }else{
            $log->action = 'updateTransaction';
            $response = $this->updateData($data, $log);
        }

        if($response['pg_status'] == 'rejected'){
            $log->error_code = 1;
            $log->error_message = $response['pg_description'];
        }

        $log->response = json_encode($response);
        $log->save();

        return $response;
    }

    private function updateData($data,TransactionLog &$log){
        $url = (isset($_SERVER['REQUEST_URI']))? $_SERVER['REQUEST_URI'] : Url::toRoute(['/main/payment/check', 'driver' => $this->driver], true);
        $response = [
            'pg_salt' => substr(md5(time()),0,16),
            'pg_status' => 'rejected',
            'pg_sig' => '',
        ];

        if($data['pg_sig'] == $this->getSignature((array)$data, $url)){
            $data = (array)$data;

            if(isset($data['pg_order_id']) && !empty($data['pg_order_id'])){
                $transaction = Transactions::findOne(intval($data['pg_order_id']));
                if(!empty($transaction)){
                    $log->transaction_id = $transaction->id;
                    if($transaction->amount == $data['pg_amount'] && $data['pg_currency'] == $this->currency){
                        if($data['pg_result']){
                            $transaction->status = 2;
                            $response['pg_status'] = 'ok';
                        }else{
                            $transaction->status = 3;
                            $response['pg_description'] = $data['pg_failure_description'];
                        }
                        $transaction->save(false);
                    }else{
                        $response['pg_description'] = Yii::$app->mv->gt('Не верная валюта или сумма',[],false);
                    }
                }else{
                    $response['pg_description'] = Yii::$app->mv->gt('Транзакция не найдена',[],false);
                }
            }else{
                $response['pg_description'] = Yii::$app->mv->gt('pg_order_id обязательно для заполнения',[],false);
            }
        }else{
            $response['pg_description'] = Yii::$app->mv->gt('Не верная подпись запроса',[],false);
        }

        $response['pg_sig'] = $this->getSignature($response, $url);

        return $response;
    }

    private function checkData($data,TransactionLog &$log){
        $url = (isset($_SERVER['REQUEST_URI']))? $_SERVER['REQUEST_URI'] : Url::toRoute(['/main/payment/check', 'driver' => $this->driver], true);
        $response = [
            'pg_salt' => substr(md5(time()),0,16),
            'pg_status' => 'rejected',
            'pg_sig' => '',
        ];
        if($data['pg_sig'] == $this->getSignature((array)$data, $url)){
            $data = (array)$data;

            if(isset($data['pg_order_id']) && !empty($data['pg_order_id'])){
                $transaction = Transactions::findOne(intval($data['pg_order_id']));
                if(!empty($transaction)){
                    $log->transaction_id = $transaction->id;
                    if($transaction->amount == $data['pg_amount'] && $data['pg_currency'] == $this->currency){
                        $transaction->status = 1;
                        $transaction->save(false);
                        $response['pg_status'] = 'ok';
                    }else{
                        $response['pg_description'] = Yii::$app->mv->gt('Не верная валюта или сумма',[],false);
                    }
                }else{
                    $response['pg_description'] = Yii::$app->mv->gt('Транзакция не найдена',[],false);
                }
            }else{
                $response['pg_description'] = Yii::$app->mv->gt('pg_order_id обязательно для заполнения',[],false);
            }
        }else{
            $response['pg_description'] = Yii::$app->mv->gt('Не верная подпись запроса',[],false);
        }

        $response['pg_sig'] = $this->getSignature($response, $url);

        return $response;
    }

    public static function toXML($data,SimpleXMLElement &$xml_data){
        foreach( $data as $key => $value ) {
            if( is_numeric($key) ){
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
            if( is_array($value) ) {
                $subnode = $xml_data->addChild($key);
                self::toXML($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }

    private function getSignature(array $data, $url)
    {
        preg_match_all('/\/([\w.-]+)[?]?/m',$url, $matches);

        if(empty($matches)){
            throw new \Exception(Yii::$app->mv->gt('Платежная система: Не верный платежный метод',[],false));
        }else{
            $action = array_pop($matches[1]);
        }

        unset($data['pg_sig']);
        ksort($data, SORT_STRING);
        array_unshift($data, $action);
        array_push($data, $this->key);
        $signString = implode(';', $data);

        return md5($signString);
    }
}