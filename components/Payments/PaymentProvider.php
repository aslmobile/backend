<?php namespace app\components\Payments;

class PaymentProvider
{
    public function __construct()
    {

    }

    public static function getDriver($params)
    {
        if (!isset($params['driver'])) throw new \Exception('Драйвер не найден!');
        else
        {
            $class = '\\' . __NAMESPACE__ . '\\Drivers\\' . $params['driver'];

            if (class_exists($class)) return new $class($params);
            else throw new \Exception('Драйвер не найден!');
        }
    }
}