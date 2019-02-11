<?php

namespace app\components\Socket;

use app\components\ArrayQuery\ArrayQuery;
use app\components\Socket\models\Line;
use app\models\Devices;
use app\models\Notifications;
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
        /** @var \app\models\Trip|bool $d_trip */

        $d_trip = Trip::find()->where([
            'user_id' => $device->user_id,
            'status' => Trip::STATUS_CREATED
        ])->one();

        if ($d_trip) {

            $queue_position = 1;

            $trips = Trip::find()->where(['status' => Trip::STATUS_CREATED, 'route_id' => $d_trip->route_id]);
            if ($d_trip->vehicle_type_id != 0) $trips = $trips->andWhere(['vehicle_type_id' => $d_trip->vehicle_type_id]);
            $trips = $trips->orderBy(['created_at' => SORT_ASC, 'seats' => SORT_DESC])->all();

            if ($trips && count($trips)) foreach ($trips as $trip) {
                if ($trip->id == $d_trip->id) break;
                $queue_position++;
            }

            $vehicles_queue = Line::find()
                ->where(['status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]])
                ->andWhere(['route_id' => $d_trip->route_id]);
            if ($d_trip->vehicle_type_id != 0) $vehicles_queue = $vehicles_queue->andWhere(['vehicle_type_id' => $d_trip->vehicle_type_id]);
            $vehicles_queue = $vehicles_queue->count();

            $basic_estimated_time = $queue_position * 300;
            $estimated_time = $basic_estimated_time * 3 / ($vehicles_queue ? $vehicles_queue : 1);
            if ($estimated_time < 900) $estimated_time = 900;

            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => [
                    'queue_position' => $queue_position,
                    'estimated_time' => $estimated_time,
                    'trip_id' => $d_trip->id
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

        $queue = [];

        /** @var Line $line */
        $line = Line::find()
            ->where(['driver_id' => $device->user_id])
            ->andWhere(['status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if (!empty($line)) {
            $lines = Line::find()
                ->where(['status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]])
                ->andWhere(['route_id' => $line->route_id])
                ->andWhere(['vehicle_type_id' => $line->vehicle_type_id])
                ->orderBy(['freeseats' => SORT_ASC, 'created_at' => SORT_ASC])
                ->all();
            foreach ($lines as $line) $queue[] = $line->toArray();
        }

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
    public function checkOnline($data, $from, $connections)
    {
        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);

        $user_id = $data['data']['user_id'];

//        $id = null;
//        $query = new ArrayQuery();
//        $query->from($connections);
//        $device = $query->where(['device.user_id' => intval($user_id)])->one();
//        if(!empty($device)) $id = $device->device->user_id;

        $response = [
            'message_id' => $this->message_id,
            'device_id' => 0,
            'user_id' => 0,
            'data' => $user_id
        ];

        $this->addressed = [0];

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
        $line = Line::find()->where([
            'AND',
            ['=', 'driver_id', $device->user_id],
            ['IN', 'status', [Line::STATUS_IN_PROGRESS, Line::STATUS_WAITING]]
        ])->orderBy(['created_at' => SORT_DESC])->one();

        if ($line) {

            $line->position = $position;
            $line->angle = $angle;
            $line->save();

            /** @var \app\models\Trip $trips */
            $trips = ArrayHelper::getColumn(
                Trip::find()->where([
                    'line_id' => $line->id,
                    'status' => [Trip::STATUS_WAY, Trip::STATUS_WAITING]
                ])->andWhere(['<=', 'queue_time', time()])->all(),
                'user_id');
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
    public function startScheduledTrip($data, $from, $connections)
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
                'trip' => $trip_data
            ]
        ];

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [$device->user_id];

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

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [$device->user_id];

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

        $watchdog = RestFul::find()->where([
            'AND',
            ['=', 'type', RestFul::TYPE_PASSENGER_ACCEPT_SEAT],
            ['=', 'user_id', $device->user_id],
            ['=', 'message', json_encode(['status' => 'request'])],
            ['>', 'created_at', time() - 300],
        ])->one();

        if (isset($data['data']['trip']) && !empty($data['data']['trip']))
            $trip_data = $data['data']['trip']; else $trip_data = null;

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => !empty($watchdog) ? [
                'seat_from' => $watchdog->created_at,
                'seat_time' => 300,
                'trip' => $trip_data
            ] : null
        ];

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [$device->user_id];

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

        if (isset($data['data']['trip']) && !empty($data['data']['trip']))
            $trip_data = $data['data']['trip']; else $trip_data = null;

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => [
                'decline_from' => time(),
                'decline_time' => 300,
                'trip' => $trip_data
            ]
        ];

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [$device->user_id];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function cancelDriverTrip($data, $from, $connections)
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
                'decline_from' => time(),
                'decline_time' => 300,
                'trip' => $trip_data
            ]
        ];

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [$device->user_id];

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

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [$device->user_id];

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

            $user_device = clone $device;

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

            $this->addressed = $data['data']['addressed'];

        } else {

            $line = Line::find()->where([
                'status' => [Line::STATUS_WAITING, Line::STATUS_IN_PROGRESS],
                'driver_id' => $device->user_id
            ])->orderBy(['created_at' => SORT_DESC])->one();
            $line = !empty($line) ? $line->toArray() : null;

            $this->addressed = [$device->user_id];

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
                    'current_time' => time(),
                    'line' => $line_data
                ]
            ];

            if (isset($data['data']['timer']) && $data['data']['timer'] && isset($user_device)) {

                $this->loop->addTimer(300, function () use ($line, $connections, $response, $user_device) {

                    $line = \app\modules\api\models\Line::findOne(['id' => $line['id'], 'status' => Line::STATUS_WAITING]);

                    if (!empty($line)) {

                        $line->status = Line::STATUS_CANCELED;
                        $line->penalty = 1;
                        $line->save();

                        $trips = Trip::find()->where(['line_id' => $line->id])->andWhere(['status' => [Trip::STATUS_CREATED, Trip::STATUS_WAITING]])->all();

                        if (!empty($trips)) {

                            $ids = ArrayHelper::getColumn($trips, 'user_id');

                            /** @var Trip $trip */
                            foreach ($trips as $trip) {
                                $trip->driver_id = 0;
                                $trip->vehicle_id = 0;
                                $trip->line_id = 0;
                                $trip->status = Trip::STATUS_CREATED;
                                $trip->save();
                            }

                            RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
                                'AND',
                                ['user_id' => $ids],
                                ['type' => [RestFul::TYPE_PASSENGER_ACCEPT, RestFul::TYPE_PASSENGER_ACCEPT_SEAT]]
                            ]);

                            $query = new ArrayQuery();
                            $query->from($connections);
                            $devices = $query->where(['device.user_id' => $ids])->all();

                            $send_response = [
                                'action' => 'disbandedTrip',
                                'error_code' => 0,
                                'data' => [
                                    'message_id' => time(),
                                    'device_id' => $user_device->id,
                                    'user_id' => $user_device->user_id,
                                    'data' => [
                                        'disband_from' => time(),
                                        'disband_time' => 300,
                                    ]
                                ]
                            ];

                            if (!empty($devices)) foreach ($devices as $device) $device->send(base64_encode(json_encode($send_response)));

                            $notifications = Notifications::create(
                                Notifications::NT_TRIP_DISBANDED, $ids,
                                \Yii::t('app', "Не все участники поездки подтвердили поездку. Вы поедете на ближайшей свободной машине.")
                            );
                            if (is_array($notifications)) foreach ($notifications as $notification) Notifications::send($notification);

                        };
                        Queue::processingQueue();
                    }
                    unset($user_device);
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

        if (isset($data['data']['line']) && !empty($data['data']['line'])) {
            $line_data = $data['data']['line'];
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

        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [$device->user_id];

        return $response;
    }

    /**
     * @param $data
     * @param $from
     * @param $connections
     * @return array
     */
    public function disbandedTrip($data, $from, $connections)
    {
        /** @var Devices $device */
        if ($this->validateDevice($from)) $device = $from->device;

        if (isset ($data['data']['message_id'])) $this->message_id = intval($data['data']['message_id']);
        $this->addressed = isset($data['data']['addressed']) ? $data['data']['addressed'] : [$device->user_id];

        if (isset($data['data']['line']) && !empty($data['data']['line'])) {

            $trip_id = intval($data['data']['trip_id']);
            $line_data = $data['data']['line'];

            $response = [
                'message_id' => time(),
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => [
                    'disband_from' => time(),
                    'disband_time' => 300,
                ]
            ];
            /** @var \app\modules\api\models\Line $line */
            $line = \app\modules\api\models\Line::findOne([
                'id' => intval($line_data['id']),
                'status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]
            ]);

            /** @var Trip[] $trips */
            $trips = Trip::find()->where([
                'line_id' => intval($line_data['id']),
                'status' => [Trip::STATUS_CREATED, Trip::STATUS_WAITING, Trip::STATUS_WAY]
            ])->andWhere(['!=', 'id', $trip_id])->all();

            $ids = [];

            if (!empty($trips)) {
                $ids = ArrayHelper::getColumn($trips, 'user_id');
                /** @var Trip $trip */
                foreach ($trips as $trip) {
                    $trip->vehicle_id = 0;
                    $trip->line_id = 0;
                    $trip->driver_id = 0;
                    $trip->status = Trip::STATUS_CREATED;
                    $trip->save();
                }
                RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
                    'AND',
                    ['user_id' => $ids],
                    ['type' => [RestFul::TYPE_PASSENGER_ACCEPT, RestFul::TYPE_PASSENGER_ACCEPT_SEAT]]
                ]);
            }

            if (!empty($line)) {
                $ids = $ids + [$line->driver_id];
                $line->status = Line::STATUS_QUEUE;
                $line->freeseats = $line->seats;
                $line->save();
                RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
                    'AND',
                    ['user_id' => $line->driver_id],
                    ['type' => [RestFul::TYPE_DRIVER_ACCEPT]]
                ]);
            }

            $notifications = Notifications::create(
                Notifications::NT_TRIP_DISBANDED, $ids,
                \Yii::t('app', "Не все участники поездки подтвердили поездку. Вы поедете на ближайшей свободной машине.")
            );
            if (is_array($notifications)) foreach ($notifications as $notification) Notifications::send($notification);

            $this->addressed = $ids;

        } else {

            $response = [
                'message_id' => $this->message_id,
                'device_id' => $device->id,
                'user_id' => $device->user_id,
                'data' => null
            ];
            $this->error_code = 2;

        }

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
        $line_data = [];
        $timer = isset($data['data']['timer']) ? $data['data']['timer'] : false;

        if (isset($data['data']['passenger'])) {

            $passenger = intval($data['data']['passenger']);
            $line_data = $data['data']['line'];
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
                    'target_id' => $line_data['id'],
                    'uip' => '0.0.0.0'
                ]);
                $watchdog->save();
            }

            if ($timer) {
                $this->loop->addTimer(300, function () use ($passenger, $line_data, $connections, $device) {

                    /** @var Trip $trip */
                    $trip = Trip::find()->where([
                        'user_id' => $passenger,
                        'line_id' => intval($line_data['id']),
                        'status' => Trip::STATUS_CREATED
                    ])->andWhere(['driver_id' => 0])->one();

                    if (!empty($trip)) {

                        /** @var \app\modules\api\models\Line $line */
                        $line = \app\modules\api\models\Line::findOne([
                            'id' => intval($line_data['id']),
                            'status' => [Line::STATUS_QUEUE, Line::STATUS_WAITING]
                        ]);

                        $not = !empty($trip->not) ? json_decode($trip->not) : [];
                        $not[] = $line_data['id'];
                        $trip->not = json_encode($not);
                        $trip->save();

                        /** @var Trip[] $trips */
                        $trips = Trip::find()->where([
                            'line_id' => intval($line_data['id']),
                            'status' => [Trip::STATUS_CREATED, Trip::STATUS_WAITING]
                        ])->all();

                        $ids = [];

                        if (!empty($trips)) {
                            $ids = ArrayHelper::getColumn($trips, 'user_id');
                            foreach ($trips as $trip) {
                                $trip->vehicle_id = 0;
                                $trip->line_id = 0;
                                $trip->driver_id = 0;
                                $trip->status = Trip::STATUS_CREATED;
                                $trip->save();
                            }
                            RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
                                'AND',
                                ['user_id' => $ids],
                                ['type' => [RestFul::TYPE_PASSENGER_ACCEPT, RestFul::TYPE_PASSENGER_ACCEPT_SEAT]]
                            ]);
                        }

                        if (!empty($line)) {
                            RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
                                'AND',
                                ['user_id' => $line->driver_id],
                                ['type' => [RestFul::TYPE_DRIVER_ACCEPT]]
                            ]);
                            $line->status = Line::STATUS_QUEUE;
                            $line->freeseats = $line->seats;
                            $line->save();
                        }

                        $query = new ArrayQuery();
                        $query->from($connections);
                        $devices = $query->where(['device.user_id' => [$line_data['driver_id']] + $ids])->all();

                        $send_response = [
                            'action' => 'disbandedTrip',
                            'error_code' => 0,
                            'data' => [
                                'message_id' => time(),
                                'device_id' => $device->id,
                                'user_id' => $device->user_id,
                                'data' => [
                                    'disband_from' => time(),
                                    'disband_time' => 300,
                                ]
                            ]
                        ];

                        if (!empty($devices)) foreach ($devices as $device) $device->send(base64_encode(json_encode($send_response)));

                        $notifications = Notifications::create(
                            Notifications::NT_TRIP_DISBANDED, [$line_data['driver_id']] + $ids,
                            \Yii::t('app', "Возвращение в очередь. Поездка была расформирована.")
                        );
                        if (is_array($notifications)) foreach ($notifications as $notification) Notifications::send($notification);

                        Queue::processingQueue();

                    }

                });
            }

            $this->addressed = $data['data']['addressed'];

        } else {

            $watchdog = RestFul::findOne([
                'type' => RestFul::TYPE_PASSENGER_ACCEPT,
                'user_id' => $device->user_id,
                'message' => json_encode(['status' => 'request'])
            ]);
            if (!empty($watchdog)) {
                $line_data = \app\modules\api\models\Line::findOne($watchdog->target_id);
                $line_data = !empty($line_data) ? $line_data->toArray() : [];
            }

            $this->addressed = [$device->user_id];

        }

        $response = [
            'message_id' => $this->message_id,
            'device_id' => $device->id,
            'user_id' => $device->user_id,
            'data' => [
                'accept_from' => !empty($watchdog) ? $watchdog->created_at : time(),
                'current_time' => time(),
                'accept_time' => 300,
                'line' => $line_data,
            ]
        ];

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

        $user_device = clone $device;

        $data = $data['data'];
        $this->message_id = intval($data['message_id']);
        $line = $data['line'];
        $checkpoint = $data['checkpoint'];
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

        if ($timer && isset($user_device)) {
            $this->loop->addTimer(300, function () use ($line, $checkpoint, $connections, $response, $user_device) {

                /** @var Trip $trip */
                $trips = Trip::find()->where([
                    'startpoint_id' => intval($checkpoint['id']),
                    'line_id' => intval($line['id']),
                    'status' => Trip::STATUS_WAITING
                ])->all();

                if (!empty($trips)) {
                    foreach ($trips as $trip) {

                        $trip->status = Trip::STATUS_CANCELLED;
                        $trip->driver_description = \Yii::t('app', "Вам отказано в поездке. Причина - опоздание.");
                        $trip->penalty = 1;
                        $trip->save();

                        RestFul::updateAll(['message' => json_encode(['status' => 'closed'])], [
                            'AND',
                            ['user_id' => $trip->user_id],
                            ['type' => [RestFul::TYPE_PASSENGER_ACCEPT, RestFul::TYPE_PASSENGER_ACCEPT_SEAT]]
                        ]);

                        /** @var \app\modules\api\models\Line $line */
                        $line = \app\modules\api\models\Line::findOne($trip->line_id);
                        if (!empty($line)) {
                            $line->freeseats += $trip->seats;
                            $line->save();
                        }

                        $query = new ArrayQuery();
                        $query->from($connections);
                        $devices = $query->where(['device.user_id' => intval($trip->user_id)])->all();

                        $send_response = [
                            'action' => 'declineByTimer',
                            'error_code' => 0,
                            'data' => [
                                'message_id' => $this->message_id,
                                'device_id' => $user_device->id,
                                'user_id' => $user_device->user_id,
                                'data' => [
                                    'decline_from' => time(),
                                    'decline_time' => 300,
                                    'trip' => $trip->toArray()
                                ]
                            ]
                        ];

                        $notifications = Notifications::create(
                            Notifications::NTP_TRIP_CANCEL, [$trip->user_id],
                            \Yii::t('app', "Вам отказано в поездке. Причина - опоздание.")
                        );
                        if (is_array($notifications)) foreach ($notifications as $notification) Notifications::send($notification);
                        if (!empty($devices)) foreach ($devices as $device) $device->send(base64_encode(json_encode($send_response)));

                    }
                }
            });
        }

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
