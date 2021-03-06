<?php

namespace app\commands;

use app\components\ConsoleController;
use app\components\Socket\SocketServer;
use app\modules\main\models\Settings;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Yii;
use yii\base\Module;

class SocketController extends ConsoleController
{
    public $port;
    public $coreSettings;

    public function options($actionID)
    {
        return ['port'];
    }

    public function optionAliases()
    {
        return [
            'p' => 'port',
            'port' => 'port'
        ];
    }

    public function __construct($id, Module $module, array $config = [])
    {
        Yii::setAlias('@webroot', __DIR__ . '../web');
        $this->coreSettings = self::getCoreSettings();

        parent::__construct($id, $module, $config);
    }

    public static function getCoreSettings()
    {
        return Settings::find()->where('id = 1')->one();
    }

    public function actionIndex()
    {
        $socket = new SocketServer();
        $ws = new WsServer($socket);
        $httpServer = new HttpServer($ws);

        $port = (!empty($this->port)) ? $this->port : \Yii::$app->params['socket']['port'];
        $server = IoServer::factory($httpServer, $port);
        $socket->loop = $server->loop;
        echo 'Server running on port: ' . $port . "\n" . date('d.m.Y h:i', time()) . "\n";
        $server->run();
    }
}
