<?php

namespace app\components\Socket;

use Ratchet\ConnectionInterface;

class Message
{

    public $message = null;
    public $error_code = 0;
    public $action;
    public $connections;

    public function __construct(ConnectionInterface $from, $data = '', $connections)
    {
        $this->connections = $connections;
        $data = base64_decode($data, true);
        $data = json_decode($data, true);

        if (!empty($data) && is_array($data) && array_key_exists('action', $data)) {

            $method = $data['action'];
            $this->action = $method;

            if (method_exists(__CLASS__, $method)) {
                $this->message = $this->$method($data, $from, $connections);
            } else {
                $this->error_code = 1;
            }
        } else {
            $this->error_code = 6;
        }
    }

    /**
     * @param $data
     * @param $from
     *
     * @return null
     */
    private function action_name($data, $from, $connections)
    {
        return null;
    }
}
