<?php namespace app\modules\api\models;

class RestFul extends \app\models\RestFul
{
    public static function updateDriverAccept()
    {
        self::updateAll(
            ['message' => json_encode(['status' => 'closed'])],
            ['AND',
                ['<=', 'created_at', time() - 300],
                ['=', 'type', RestFul::TYPE_DRIVER_ACCEPT]
            ]
        );
    }

    public static function updatePassengerAccept()
    {
        self::updateAll(
            ['message' => json_encode(['status' => 'closed'])],
            ['AND',
                ['<=', 'created_at', time() - 300],
                ['=', 'type', RestFul::TYPE_PASSENGER_ACCEPT]
            ]
        );
    }
}