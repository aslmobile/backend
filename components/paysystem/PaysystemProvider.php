<?php
/**
 * Created by PhpStorm.
 * User: Graf
 * Date: 29.03.2017
 * Time: 11:27
 */

namespace app\components\paysystem;


class PaysystemProvider
{
    private function __construct(){}

    public static function getDriver($params){
        if(!isset($params['driver'])){
            throw new \Exception('Драйвер не найден!');
        }else{
            $class = '\\'.__NAMESPACE__.'\\Drivers\\'.$params['driver'];

            if(class_exists($class))
            {
                return new $class($params);
            }else{
                throw new \Exception('Драйвер не найден!');
            }
        }
    }
}