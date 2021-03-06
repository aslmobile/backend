<?php

namespace app\components\Socket;

use app\components\ArrayQuery\ArrayQuery;
use app\models\Notifications;
use app\models\User;
use app\modules\api\models\Devices;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Yii;

class SocketServer implements MessageComponentInterface
{
    /**
     * @var array
     */
    public $devices = [];

    /**  @var \React\EventLoop\LoopInterface */
    public $loop;

    /** @var string */
    public $server;

    /**
     * SocketServer constructor.
     */
    public function __construct()
    {
        Yii::$app->db->createCommand('SET SESSION statement_timeout = 86400;');

        $timestamp = strtotime(gmdate("M d Y H:i:s", time()));
        User::updateAll(['last_activity' => $timestamp], 'last_activity IS NULL');

        $this->server = Yii::$app->params['socket']['authkey_server'];
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

        if (is_array($input_data) && array_key_exists('auth', $input_data)) {

            $authkey = $input_data['auth'];
            $device = $this->validateDevice($conn, $authkey);

            if (!$device) {
                echo "Connection closed! No device auth!\n";
                $conn->close();
            } elseif ($authkey === $this->server) {
                $conn->device = (object)['id' => 0, 'user_id' => 0];
                $this->devices += [$conn->resourceId => $conn];
                echo "Server connected.\n" . date('d.m.Y h:i', time()) . "\n";
            } else {
                $conn->device = $device;
                //if (isset($this->devices[$conn->device->id])) $this->devices[$conn->device->id]->close();
                $this->devices += [$conn->resourceId => $conn];
                Yii::$app->db->createCommand()->update('users', ['last_activity' => null], 'id = ' . $device->user_id)->execute();

                $result = new Message($conn, base64_encode(json_encode([
                    'action' => "checkOnline",
                    'data' => ['message_id' => time(), 'user_id' => $device->user_id]
                ])), $this->devices, $loop = $this->loop
                );
                $query = new ArrayQuery();
                $query->from($this->devices);
                foreach ($result->addressed as $user_id) {
                    $devices = $query->where(['device.user_id' => intval($user_id)])->all();
                    if (!empty($devices)) foreach ($devices as $device) $device->send(json_encode($result->message));
                }

                echo "Device: {$conn->device->id}; User: {$conn->device->user_id}; connected.\n" . date('d.m.Y h:i', time()) . "\n";
            }

        } else {

            echo "Connection closed! Connection data is invalid!\n";
            $conn->send(base64_encode(json_encode(['error_code' => 100, 'message' => 'Connection closed. Please verify your data.'])));
            $conn->close();

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

        if ($conn->device->id) {

            $query = new ArrayQuery();
            $query->from($this->devices);
            $online = $query
                ->where(['device.user_id' => intval($conn->device->user_id)])
                ->andWhere(['!=', 'device.id', intval($conn->device->id)])
                ->all();

            if (empty($online)) {
                Yii::$app->db->createCommand()->update('users', ['last_activity' => time()], 'id = ' . $conn->device->user_id)->execute();
                $result = new Message($conn, base64_encode(json_encode([
                    'action' => "checkOnline",
                    'data' => ['message_id' => time(), 'user_id' => $conn->device->user_id]
                ])), $this->devices, $loop = $this->loop
                );

                foreach ($result->addressed as $user_id) {
                    $devices = $query->where(['device.user_id' => intval($user_id)])->all();
                    if (!empty($devices)) foreach ($devices as $device) $device->send(json_encode($result->message));
                }
            }

        }
        echo "Device: {$conn->device->id}; User: {$conn->device->user_id}; disconnected.\n" . date('d.m.Y h:i', time()) . "\n";

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
        var_dump($e->getTraceAsString());
        echo "An error has occurred: {$e->getFile()} : {$e->getLine()} \n {$e->getMessage()}\n" . date('d.m.Y h:i', time()) . "\n";

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
        $result = new Message($from, $msg, $connections, $loop = $this->loop);

        $response = [
            'error_code' => $result->error_code,
            'action' => $result->action,
            'data' => $result->message,
        ];

        $response = json_encode($response);
        $response = base64_encode($response);

        $time = date('d.m.Y h:i:s', time()) . "
        \n ----------------------------------------- \n";

        if (empty($result->addressed)) {
            foreach ($this->devices as $device) {
                $device->send($response);
                $recipient = $device->device->id;
                echo "Response to ({$recipient})\n Action: {$result->action}\n" . $time;
            }
        } else {
            $query = new ArrayQuery();
            $query->from($this->devices);
            foreach ($result->addressed as $user_id) {
                $devices = $query->where(['device.user_id' => intval($user_id)])->all();
                if (!empty($devices)) {
                    foreach ($devices as $device) {
                        $device->send($response);
                        $recipient = $device->device->id;
                        echo "Response to ({$recipient})\n Action: {$result->action}\n" . $time;
                    }
                }
                if (is_array($result->notifications)) foreach ($result->notifications as $notification) {
                    if ($notification instanceof Notifications) Notifications::send($notification);
                }
            }
        }

        if (is_object($from->device) && !in_array($from->device->user_id, $result->addressed)) {
            $from->send($response);
            $recipient = $from->device->id;
            echo "Response to ({$recipient})\n Action: {$result->action}\n" . $time;
        }

        $sender = ($from->device->id == 0) ? 'Server' : $from->device->id;

        echo "Message from ({$sender})\n Action: {$result->action}\n" . $time;
    }

    /**
     * @param string $string
     */
    public static function log($string = '')
    {
        echo $string;
    }

    /**
     * @param $conn ConnectionInterface
     * @param $authkey string
     *
     * @return bool|null|Devices|array
     */
    private function validateDevice($conn, $authkey)
    {
        if (!empty($authkey)) {
            $device = Devices::find()->where(['auth_token' => $authkey])->one();
            if ($device && !empty($device)) return $device;
            if ($authkey === $this->server) return true;
        }

        $conn->send('Device not found. Please check your token');
        $conn->close();

        return false;
    }
}
