<?php

namespace app\components\Socket;

use Yii;
use yii\base\Model;
use Ratchet\ConnectionInterface;

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

    public function init()
    {
        $this->scheme = Yii::$app->params['socket']['scheme'];
        $this->host = Yii::$app->params['socket']['host'];
        $this->port = Yii::$app->params['socket']['port'];
        $this->authkey = Yii::$app->params['socket']['authkey_server'];
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    public function push($message)
    {
        $connection = \Ratchet\Client\connect($this->scheme . $this->host . ':' . $this->port . '?auth=' . $this->authkey);
        $connection->then(function ($conn) use ($message) {
            /** @var ConnectionInterface $conn */
            $conn->send($message);
            $conn->close();
        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n";
        });

        return true;
    }
}
