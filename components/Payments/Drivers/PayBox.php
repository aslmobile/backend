<?php
namespace app\components\Payments\Drivers;

use app\components\Payments\PaymentInterface;
use app\models\TransactionLog;
use app\models\Transactions;
use app\modules\api\models\Users;
use SimpleXMLElement;
use Yii;
use yii\db\Transaction;
use yii\helpers\Url;
use yii\web\Response;

class PayBox implements PaymentInterface
{
    # REQUESTS
    private $merchant_cardstorage_request = 'https://paybox.kz/v1/merchant/{merchant_id}/cardstorage/add';


    private $driver = 'PayBox';
    private $key = '';
    private $merchant_id = '';
    private $devMod = true;
    private $currency = 'KZT';

    public $result = 0;

    public function __construct($receipt = true)
    {
        if ($receipt) $this->key = \Yii::$app->params['payments'][$this->driver]['secret_key_receipt'];
        else $this->key = \Yii::$app->params['payments'][$this->driver]['secret_key_pay'];

        $this->merchant_id = \Yii::$app->params['payments'][$this->driver]['merchant_id'];
        $this->devMod = \Yii::$app->params['payments'][$this->driver]['dev'];

        if (isset(\Yii::$app->params['payments'][$this->driver]['currency']))
            $this->currency = \Yii::$app->params['payments'][$this->driver]['currency'];
    }

    public function getMethodUrl($url)
    {
        return str_replace(['{merchant_id}'], [$this->merchant_id], $url);
    }

    public function addCard(Users $user)
    {
        $log = new TransactionLog([
            'transaction_id' => 0,
            'driver' => $this->driver,
            'action' => 'addCard'
        ]);

        $method_url = $this->getMethodUrl($this->merchant_cardstorage_request);

        $data = [
            'pg_merchant_id'    => $this->merchant_id,
            'pg_user_id'        => $user->id,
            'pg_post_link'      => Url::toRoute(['/api/payment/paybox-knock-knock'], true),
            'pg_back_link'      => Url::toRoute(['/api/payment/paybox-knock-out'], true),
            'pg_salt'           => Yii::$app->params['salt'],
            'pg_testing_mode'   => intval($this->devMod)
        ];
        $data['pg_sig'] = $this->getSignature($data, $method_url);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $method_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        $response = curl_exec($ch);
        curl_close($ch);

        $iframe_link = false;
        if ($response = @simplexml_load_string($response))
        {
            $log->response = $response;

            if ($response->pg_sig == $this->getSignature((array)$response, $method_url))
            {
                $response = (array) $response;

                if (isset($response['pg_status']) && !empty($response['pg_status']))
                {
                    switch ($response['pg_status'])
                    {
                        case 'ok':
                            $iframe_link = $response['pg_redirect_url'];
                            break;

                        case 'rejected':
                            $log->error_code = $response['pg_error_code'];
                            $log->error_message = $response['pg_description'];
                            break;

                        case 'error':
                            $log->error_code = $response['pg_error_code'];
                            $log->error_message = $response['pg_description'];
                            break;
                    }
                }else{
                    $log->error_code = 1;
                    $log->error_message = 'Invalid server response';
                }
            }
            else
            {
                $log->error_code = 1;
                $log->error_message = 'Invalid signature';
            }
        }
        else
        {
            $log->error_code = -1;
            $log->error_message = 'Invalid response';
        }

        $log->save();

        return $iframe_link;
    }

    public static function toXML($data,SimpleXMLElement &$xml_data)
    {
        foreach ($data as $key => $value)
        {
            if (is_numeric($key)) $key = 'item' . $key;
            if (is_array($value))
            {
                $subnode = $xml_data->addChild($key);
                self::toXML($value, $subnode);
            }
            else $xml_data->addChild("$key", htmlspecialchars("$value"));
        }
    }

    private function getSignature(array $data, $url)
    {
        preg_match_all('/\/([\w.-]+)[?]?/m',$url, $matches);

        if (empty($matches)) throw new \Exception(Yii::$app->mv->gt('Платежная система: Не верный платежный метод',[],false));
        else $action = array_pop($matches[1]);

        unset($data['pg_sig']);

        ksort($data, SORT_STRING);
        array_unshift($data, $action);
        array_push($data, $this->key);
        $signString = implode(';', $data);

        return md5($signString);
    }
}