<?php

namespace app\components\Socket;

use app\modules\api\models\Device;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Yii;

class SocketServer implements MessageComponentInterface
{
    /**
     * @var array
     */
    public $devices = [];

    /**
     * SocketServer constructor.
     */
    public function __construct()
    {
        Yii::$app->db->createCommand('SET SESSION wait_timeout = 86400;')->execute();
    }

    /**
     * When a new connection is opened it will be passed to this method
     *
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     *
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        $query_string = $conn->httpRequest->getUri()->getQuery();
        parse_str($query_string, $q);
        $input_data = $q;

        if (is_array($input_data) && array_key_exists('authkey', $input_data)) {

            $authkey = $input_data['authkey'];

            $device = $this->validateDevice($conn, $authkey);

            if (!empty($device)) {

                $conn->device = $device;

                $this->devices[$conn->resourceId] = $conn;

                echo "New connection! ({$conn->device->id})\n";
            }
        }
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     *
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     *
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages

        if (isset($conn->device)) {

            if (isset($this->devices[$conn->resourceId])) {
                unset($this->devices[$conn->resourceId]);
            }
        }

        echo "Connection {$conn->device->id} has disconnected\n";
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     *
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     *
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     *
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     *
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        $connections = $this->devices;
        $result = new Message($from, $msg, $connections);

        $response = [
            'error_code' => $result->error_code,
            'action' => $result->action,
            'data' => $result->message,
        ];

        if ($response['action'] == 'sign_out') {
            unset($response['data']);
        }

        $response = json_encode($response);
        $response = base64_encode($response);

        $from->send($response);

        //foreach ($this->devices as $key => $device){
        //    if($key != $from->resourceId){
        //        $device->send($response);
        //    }
        //}

        echo "Message from ({$from->device->id})\n";
    }

    /**
     * @param string $string
     */
    public static function log($string = '')
    {
        echo $string;
    }

    /**
     * @param $conn
     * @param $authkey
     *
     * @return bool|null|Device|array
     */
    private function validateDevice($conn, $authkey)
    {
        if (!empty($authkey)) {
            $device = Device::find()->joinWith('authDevice')->where(['auth_device.authkey' => $authkey])->one();
            if (!empty($device)) {
                return $device;
            }
        }

        $conn->send('Unknown auth key');
        $conn->close();

        return false;
    }
}
