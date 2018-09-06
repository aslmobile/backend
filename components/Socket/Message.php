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

    public function passengerQueue($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;
        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        /** @var \app\models\Trip $trip */
        /** @var \app\models\Trip|bool $device_trip */

        $device_trip = Trip::find()->andWhere([
            'AND',
            ['=', 'user_id', $device->user_id],
            ['=', 'status', Trip::STATUS_WAITING]
        ])->one();

        if ($device_trip)
        {
            $queue_position = 1;

            $trips = Trip::find()->andWhere([
                'AND',
                ['=', 'status', Trip::STATUS_WAITING],
                ['=', 'route_id', $device_trip->route_id]
            ])
                ->orderBy([
                    'created_at' => SORT_DESC
                ])
                ->all();

            if ($trips && count($trips)) foreach ($trips as $trip)
            {
                if ($trip->id == $device_trip->id) break;

                $queue_position++;
            }

            $vehicles_queue = Line::find()->andWhere(['AND', ['=', 'status', Line::STATUS_WAITING]])->count();
            $basic_estimated_time = $queue_position * 300;
            $estimated_time = $basic_estimated_time * 3 / $vehicles_queue;

            if ($estimated_time < 900) $estimated_time = 900;

            $response = [
                'message_id'    => $this->message_id,
                'device_id'     => $device->id,
                'user_id'       => $device->user_id,
                'data'          => [
                    'queue_position'    => $queue_position,
                    'estimated_time'    => $estimated_time,
                    'trip_id'           => $device_trip->id
                ]
            ];
        }
        else
        {
            $response = [
                'message_id'    => $this->message_id,
                'device_id'     => $device->id,
                'user_id'       => $device->user_id,
                'data'          => [
                    'queue_position'    => -1,
                    'estimated_time'    => -1
                ]
            ];
        }

        return $response;
    }

    public function driverAnglePosition($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;
        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $position['lat'] = $lat = (isset ($data['data']['lat']) && !empty ($data['data']['lat'])) ? $data['data']['lat'] : '0,0';
        $position['lng'] = $lng = (isset ($data['data']['lng']) && !empty ($data['data']['lng'])) ? $data['data']['lng'] : '0,0';

        $position = implode(';', $position);
        $angle = (isset ($data['data']['angle']) && !empty ($data['data']['angle'])) ? $data['data']['angle'] : '0,0';

        /** @var \app\models\Line $line */
        $line = Line::find()->andWhere([
            'AND',
            ['=', 'driver_id', $device->user_id],
            ['=', 'status', Line::STATUS_IN_PROGRESS]
        ])->one();

        if ($line)
        {
            $line->position = $position;
            $line->angle = $angle;
            $line->save();

            $response = [
                'message_id'    => $this->message_id,
                'device_id'     => $device->id,
                'user_id'       => $device->user_id,
                'data'          => [
                    'lat'       => $lat,
                    'lng'       => $lng,
                    'angle'     => $angle,
                    'line_id'   => $line->id
                ]
            ];
        }
        else
        {
            $response = [
                'message_id'    => $this->message_id,
                'device_id'     => $device->id,
                'user_id'       => $device->user_id,
                'data'          => [
                    'lat'   => $lat,
                    'lng'   => $lng,
                    'angle' => $angle,
                    'line_id'  => 0
                ]
            ];
        }

        return $response;
    }

    public function driverQueue($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;
        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $lines = Line::find()->where(['status' => Line::STATUS_QUEUE, 'driver_id' => $device->user_id])->orderBy(['freeseats' => SORT_DESC, 'created_at' => SORT_DESC])->all();
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

        RestFul::updateDriverAccept();

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

        /** @var \app\models\Line $line */
        $line = \app\models\Line::find()->andWhere([
            'AND',
            ['=', 'driver_id', $device->user_id],
            ['=', 'status', Line::STATUS_WAITING]
        ])->one();

        if ($line) $line_data = $line->toArray();
        else $line_data = [];

        $response = [
            'message_id'    => $this->message_id,
            'device_id'     => $device->id,
            'user_id'       => $device->user_id,
            'data'          => [
                'accept_from'   => $watchdog->created_at,
                'accept_time'   => 300,
                'trip'          => $line_data
            ]
        ];

        return $response;
    }

    public function processingQuery($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $response = [
            'message_id'    => $this->message_id,
            'device_id'     => $device->id,
            'user_id'       => $device->user_id,
            'data'          => [
                'passengers'   => 0
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
