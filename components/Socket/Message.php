<?php

namespace app\components\Socket;

use app\components\Socket\models\Line;
use app\models\Devices;
use app\modules\api\models\RestFul;
use app\modules\api\models\Trip;
use Ratchet\ConnectionInterface;

class Message
{

    public $message = null;
    public $error_code = 0;
    public $action;
    public $connections;

    public $message_id = 0;

    public function __construct(ConnectionInterface $from, $data = '', $connections)
    {
        $this->connections = $connections;
        $data = base64_decode($data, true);
        $data = json_decode($data, true);

        if (!empty($data) && is_array($data) && array_key_exists('action', $data))
        {
            $method = $data['action'];
            $this->action = $method;

            if (method_exists(__CLASS__, $method)) $this->message = $this->$method($data, $from, $connections);
            else $this->error_code = 1;
        }
        else $this->error_code = 6;
    }

    /**
     * @param $data array
     * @param $from ConnectionInterface
     * @param $connections array
     *
     * @return array
     */
    public function ping($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $response = [
            'message_id'    => $this->message_id,
            'device_id'     => $device->id,
            'user_id'       => $device->user_id,
            'data'          => [
                'message'   => 'pong'
            ]
        ];

        return $response;
    }

    public function driverQueue($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $lines = Line::find()->where(['status' => Line::STATUS_QUEUE])->orderBy(['freeseats' => SORT_DESC, 'created_at' => SORT_DESC])->all();
        $queue = [];
        foreach ($lines as $line) $queue[] = $line->toArray();

        $response = [
            'message_id'    => $this->message_id,
            'device_id'     => $device->id,
            'user_id'       => $device->user_id,
            'data'          => [
                'queue'   => $queue
            ]
        ];

        return $response;
    }

    public function acceptDriverTrip($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        RestFul::updateAll(
            ['message' => json_encode(['status' => 'closed'])],
            ['AND',
                ['<=', 'created_at', time() - 300],
                ['=', 'type', RestFul::TYPE_DRIVER_ACCEPT]
            ]
        );

        $watchdog = RestFul::find()->where(['type' => RestFul::TYPE_DRIVER_ACCEPT, 'user_id' => $device->user->id, 'message' => json_encode(['status' => 'request'])])->one();
        if (!$watchdog)
        {
            $watchdog = new RestFul([
                'type' => RestFul::TYPE_DRIVER_ACCEPT,
                'message' => json_encode(['status' => 'request']),
                'user_id' => $device->user->id,
                'uip' => '0.0.0.0'
            ]);

            $watchdog->save();
        }

        $trip = Trip::find()->where(['status' => Trip::STATUS_WAITING, 'driver_id' => $device->user->id])->one();

        $response = [
            'message_id'    => $this->message_id,
            'device_id'     => $device->id,
            'user_id'       => $device->user_id,
            'data'          => [
                'accept_from'   => $watchdog->created_at,
                'accept_time'   => 300,
                'trip'          => $trip ? $trip->toArray() : []
            ]
        ];

        return $response;
    }

    /**
     * @param $conn ConnectionInterface
     *
     * @return bool
     */
    public function validateDevice($conn)
    {
        if (!isset ($conn->device) || empty ($conn->device) || !$conn->device)
        {
            $conn->send('Device not valid!');
            $conn->close();

            return false;
        }

        return true;
    }
}
