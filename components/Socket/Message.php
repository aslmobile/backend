<?php

namespace app\components\Socket;

use app\components\Socket\models\Line;
use app\models\Checkpoint;
use app\models\Devices;
use app\models\Queue;
use app\modules\api\models\RestFul;
use app\modules\api\models\Trip;
use Ratchet\ConnectionInterface;
use yii\helpers\ArrayHelper;

class Message
{

    public $message = null;
    public $error_code = 0;
    public $action;
    public $connections;
    public $addressed = [];
    public $notifications = [];

    /** @var \React\EventLoop\LoopInterface */
    public $loop;

    public $message_id = 0;

    /**
     * Message constructor.
     * @param ConnectionInterface $from
     * @param string $data
     * @param $connections
     * @param \React\EventLoop\LoopInterface $loop
     */
    public function __construct(ConnectionInterface $from, $data = '', $connections, \React\EventLoop\LoopInterface $loop)
    {
        $this->connections = $connections;
        $this->loop = $loop;
        $data = base64_decode($data, true);
        $data = json_decode($data, true);

        if (!empty($data) && is_array($data)) {
            if (array_key_exists('notifications', $data)) $this->notifications = $data['notifications'];
        } else $this->error_code = 6;

        if (!empty($data) && is_array($data) && array_key_exists('action', $data)) {
            $method = $data['action'];
            $this->action = $method;

            if (method_exists(__CLASS__, $method)) $this->message = $this->$method($data, $from, $connections);
            else $this->error_code = 1;
        } else $this->error_code = 6;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function ping($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => [
                'message' => 'pong'
            ]
        ];

        $this->addressed = [$device->user_id];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
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
            ['=', 'status', Trip::STATUS_CREATED]
        ])->orderBy(['created_at' => SORT_DESC])->one();

        if ($device_trip) {

            $queue_position = 1;

            $trips = Trip::find()->andWhere([
                'AND',
                ['=', 'status', Trip::STATUS_CREATED],
                ['=', 'route_id', $device_trip->route_id]
            ])->orderBy(['created_at' => SORT_DESC, 'seats' => SORT_DESC])->all();

            if ($trips && count($trips)) foreach ($trips as $trip) {
                if ($trip->id == $device_trip->id) break;
                $queue_position++;
            }

            $vehicles_queue = Line::find()->andWhere(['status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]])
                ->andWhere(['>=', 'freeseats', $device_trip->seats])->count();

            $basic_estimated_time = $queue_position * 300;
            $estimated_time = $basic_estimated_time * 3 / $vehicles_queue;
            if ($estimated_time < 900) $estimated_time = 900;

            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => [
                    'queue_position' => $queue_position,
                    'estimated_time' => $estimated_time,
                    'trip_id' => $device_trip->id
                ]
            ];
        } else {
            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => [
                    'queue_position' => -1,
                    'estimated_time' => -1
                ]
            ];
        }

        $this->addressed = [$device->user_id];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function driverQueue($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;
        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $lines = Line::find()
            ->where(['status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING], 'driver_id' => $device->user_id])
            ->andWhere(['>', 'freeseats', 0])
            ->orderBy(['freeseats' => SORT_ASC, 'created_at' => SORT_DESC])->all();
        $queue = [];
        foreach ($lines as $line) $queue[] = $line->toArray();

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => [
                'queue' => $queue
            ]
        ];

        $this->addressed = [$device->user_id];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function changeQueue($data, $from, $connections)
    {
        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $response = [
            'message_id' => $this->message_id,
            'device_id' => 0,
            'user_id' => 0,
            'data' => null
        ];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function processingQuery($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $line = Line::find()->where(['driver_id' => $device->user_id])->orderBy(['created_at' => SORT_DESC])->one();
        $passengers = 0;

        if (!empty($line)) {
            $passengers = intval(Trip::find()->where([
                'driver_id' => $device->user_id,
                'line_id' => $line->id,
                'status' => [Trip::STATUS_WAITING, Trip::STATUS_WAY]
            ])->count('id'));
        }

        // TODO: обработка очереди (приходит с девайсов на создание поездки и поиск пассажиров)

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => [
                'passengers' => $passengers
            ]
        ];

        $this->addressed = [$device->user_id];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
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

        if ($line) {

            $line->position = $position;
            $line->angle = $angle;
            $line->save();

            /** @var \app\models\Trip $trip */
            $trips = ArrayHelper::getColumn(Trip::findAll([
                'line_id' => $line->id,
                'status' => [Trip::STATUS_WAY, Trip::STATUS_WAITING],
            ]), 'user_id');
            $this->addressed = $trips;

            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => [
                    'lat' => $lat,
                    'lng' => $lng,
                    'angle' => $angle,
                    'line_id' => $line->id
                ]
            ];

        } else {
            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => [
                    'lat' => $lat,
                    'lng' => $lng,
                    'angle' => $angle,
                    'line_id' => 0
                ]
            ];
            $this->addressed = [$device->user_id];
        }

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function acceptPassengerTrip($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        if (isset($data['data']['trip']) && !empty($data['data']['trip']))
            $trip_data = $data['data']['trip']; else $trip_data = null;

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => [
                'accept_from' => time(),
                'accept_time' => 300,
                'trip' => $trip_data
            ]
        ];

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function acceptPassengerSeat($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        RestFul::updatePassengerAccept();

        $watchdog = RestFul::findOne([
            'type' => RestFul::TYPE_PASSENGER_ACCEPT_SEAT,
            'user_id' => $device->user->id,
            'message' => json_encode(['status' => 'request'])
        ]);
        if (!$watchdog) {
            $watchdog = new RestFul([
                'type' => RestFul::TYPE_PASSENGER_ACCEPT_SEAT,
                'message' => json_encode(['status' => 'request']),
                'user_id' => $device->user->id,
                'uip' => '0.0.0.0'
            ]);
            $watchdog->save();
        }

        if (isset($data['data']['trip']) && !empty($data['data']['trip']))
            $trip_data = $data['data']['trip']; else $trip_data = null;

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => [
                'seat_from' => $watchdog->created_at,
                'seat_time' => 300,
                'trip' => $trip_data
            ]
        ];

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function declinePassengerTrip($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        RestFul::updatePassengerAccept();

        $watchdog = RestFul::findOne([
            'type' => RestFul::TYPE_PASSENGER_DECLINE,
            'user_id' => $device->user->id,
            'message' => json_encode(['status' => 'cancel'])
        ]);
        if (!$watchdog) {
            $watchdog = new RestFul([
                'type' => RestFul::TYPE_PASSENGER_DECLINE,
                'message' => json_encode(['status' => 'cancel']),
                'user_id' => $device->user->id,
                'uip' => '0.0.0.0'
            ]);
            $watchdog->save();
        }

        if (isset($data['data']['trip']) && !empty($data['data']['trip']))
            $trip_data = $data['data']['trip']; else $trip_data = null;

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => [
                'decline_from' => $watchdog->created_at,
                'decline_time' => 300,
                'trip' => $trip_data
            ]
        ];

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function pathChanged($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        if (isset($data['data']['path']) && !empty($data['data']['path']))
            $path_data = $data['data']['path']; else $path_data = null;

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => ['path' => $path_data]
        ];

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function acceptDriverTrip($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        RestFul::updateDriverAccept();

        /** @var Line $line */
        if (isset($data['data']['line'])) {
            $line = $data['data']['line'];
            $watchdog = RestFul::findOne([
                'type' => RestFul::TYPE_DRIVER_ACCEPT,
                'user_id' => $device->user->id,
                'message' => json_encode(['status' => 'request'])
            ]);
            if (!$watchdog) {
                $watchdog = new RestFul([
                    'type' => RestFul::TYPE_DRIVER_ACCEPT,
                    'message' => json_encode(['status' => 'request']),
                    'user_id' => $device->user->id,
                    'uip' => '0.0.0.0'
                ]);
                $watchdog->save();
            }
        } else {
            $line = \app\modules\api\models\Line::find()->where([
                'status' => [
                    \app\modules\api\models\Line::STATUS_WAITING,
                    \app\modules\api\models\Line::STATUS_IN_PROGRESS
                ],
                'driver_id' => $device->user_id
            ])->orderBy(['created_at' => SORT_DESC])->one();
            $line = !empty($line) ? $line->toArray() : null;
        }

        if (!empty($line)) {

            $line_data = $line;
            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => [
                    'accept_from' => isset($watchdog) ? $watchdog->created_at : time(),
                    'accept_time' => 300,
                    'line' => $line_data
                ]
            ];

            if (isset($data['data']['timer']) && $data['data']['timer']) {
                $this->loop->addTimer(300, function ($timer) use ($line) {
                    $line = \app\modules\api\models\Line::findOne($line['id']);
                    if (!empty($line) && $line->status !== Line::STATUS_IN_PROGRESS) {
                        $line->status = Line::STATUS_CANCELED;
                        $line->penalty = 1;
                        $line->save();
                        /** @var Trip $trip */
                        $trips = Trip::find()->where(['line_id' => $line->id])->all();
                        if (!empty($trips)) {
                            foreach ($trips as $trip) {
                                $trip->cancel_reason = 0;
                                $trip->driver_comment = '';
                                $trip->status = Trip::STATUS_CANCELLED_DRIVER;
                                $trip->save();
                            }
                        };
                        Queue::processingQueue();
                    }
                });
            }

        } else {

            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => null
            ];
            $this->error_code = 2;
        }

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function startDriverTrip($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        if (isset($data['data']['line']) && !empty($line)) {
            $line_data = $data['data']['line'];;
            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => [
                    'line' => $line_data
                ]
            ];

        } else {
            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => null
            ];
            $this->error_code = 2;
        }

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function readyPassengerTrip($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        $this->message_id = intval($data['data']['message_id']);
        $passenger = intval($data['data']['passenger']);

        RestFul::updatePassengerAccept();

        $watchdog = RestFul::findOne([
            'type' => RestFul::TYPE_PASSENGER_ACCEPT,
            'user_id' => $passenger,
            'message' => json_encode(['status' => 'request'])
        ]);
        if (!$watchdog) {
            $watchdog = new RestFul([
                'type' => RestFul::TYPE_PASSENGER_ACCEPT,
                'message' => json_encode(['status' => 'request']),
                'user_id' => $passenger,
                'uip' => '0.0.0.0'
            ]);
            $watchdog->save();
        }

        if (isset($data['data']['line']) && !empty($data['data']['line'])) {
            $line_data = $data['data']['line'];
            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => [
                    'accept_from' => $watchdog->created_at,
                    'accept_time' => 300,
                    'line' => $line_data,
                ]
            ];
        } else {
            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => null
            ];
            $this->error_code = 2;
        }

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function checkpointArrived($data, $from, $connections)
    {

        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        $data = $data['data'];
        $this->message_id = intval($data['message_id']);

        /** @var Line $line */
        $line = $data['line'];
        /** @var Checkpoint $checkpoint */
        $checkpoint = $data['checkpoint'];
        /** @var bool $timer */
        $timer = isset($data['timer']) ? $data['timer'] : false;

        $message = ['status' => 'passed', 'checkpoint' => intval($checkpoint['id']), 'line' => intval($line['id'])];

        $params = [
            'user_id' => $device->user_id,
            'type' => RestFul::TYPE_DRIVER_CHECKPOINT_ARRIVE,
            'message' => json_encode($message),
            'uip' => '0.0.0.0'
        ];

        $watchdog = RestFul::findOne($params);
        if (empty($watchdog)) {
            $watchdog = new RestFul($params);
            $watchdog->save();
        }

        $this->addressed = isset($data['addressed']) ? $data['addressed'] : [];

        if ($timer) {
            $this->loop->addTimer(300, function ($timer) use ($line, $checkpoint) {
                /** @var Trip $trip */
                $trips = Trip::find()->where([
                    'startpoint_id' => intval($checkpoint['id']),
                    'line_id' => intval($line['id']),
                    'status' => Trip::STATUS_WAITING
                ])->all();
                if (!empty($trips)) {
                    foreach ($trips as $trip) {
                        $trip->cancel_reason = 0;
                        $trip->passenger_comment = '';
                        $trip->status = Trip::STATUS_CANCELLED;
                        $trip->penalty = 1;
                        $trip->save();
                    }
                    Queue::processingQueue();
                }
            });
        }

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => [
                'arrived_from' => $watchdog->created_at,
                'arrived_time' => 300,
                'timer' => $timer,
                'line' => $line,
                'checkpoint' => $checkpoint
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
        if (!isset ($conn->device) || empty ($conn->device) || !$conn->device) {
            $conn->send('Device not valid!');
            $conn->close();

            return false;
        }

        return true;
    }
}
