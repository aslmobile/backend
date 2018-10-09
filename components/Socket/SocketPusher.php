<?php

namespace app\components\Socket;

use Ratchet\ConnectionInterface;
use Yii;
use yii\base\Model;

/**
 * Class SocketPusher
 *
 * @property string $randomKey
 */
class SocketPusher extends Model
{
    protected $scheme;
    protected $host;
    protected $port;
    public $authkey;

    public function init($auth = false)
    {
        $this->scheme = Yii::$app->params['socket']['scheme'];
        $this->host = Yii::$app->params['socket']['host'];
        $this->port = Yii::$app->params['socket']['port'];
        $this->authkey = $this->authkey ? $this->authkey : Yii::$app->params['socket']['authkey_server'];
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    public function push($message)
    {
        $connection = \Ratchet\Client\connect($this->scheme . $this->host . ':' . $this->port . '?auth=' . $this->authkey);
        if (!$connection) {
            echo 'Connection failed';
            exit;
        }

        $connection->then(function ($conn) use ($message) {
            /** @var ConnectionInterface $conn */
            $conn->send($message);
            $conn->close();
        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n" . date('d.m.Y h:i', time()) . "\n";
            exit;
        });

        return true;
    }
}
