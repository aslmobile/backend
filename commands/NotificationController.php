<?php

namespace app\commands;

use app\components\ConsoleController;
use app\models\Notifications;
use yii\base\Module;

class NotificationController extends ConsoleController
{

    /**
     * @inheritdoc
     */
    public function __construct($id, Module $module, array $config = [])
    {
        \Yii::setAlias('@webroot', __DIR__ . '../web');
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function actionIndex()
    {
    }

    public function actionTest()
    {
        $push = \Yii::$app->push;
        $notification = Notifications::findOne(1);
        $push->ios()->send('8a27747526c52cc5a66e920981ee069baa55ae4cd769b86125a1fe39494c0788', ['aps' => [
            'alert' => $notification->text . ': ' . $notification->title,
            'sound' => 'default',
            'notification_id' => $notification->id,
            'type' => $notification->type,
        ]]);
        $push->firebase()->send('dxVCzDP7Hdg:APA91bEpaCBtoT8pIsvY0J9pSyqH2XsMDHAYR8WHaDXG35RY3gsr52tGmbtlpKIVkljC_trH0AipigFg0eF_ElGTeyeoCjEtuOAFvvz88jswlSbrYd_53Ds-RY1y2FVYYihh9445tB6f_aQeII2Kj_hrrVqGy6S95g',
            [
                'notification' => [
                    'body' => $notification->text . ': ' . $notification->title,
                    'title' => $notification->title,
                    'sound' => 'default',
                ],
                'priority' => 'high',
                'data' => [
                    'notification_id' => $notification->id,
                    'type' => $notification->type,
                ],
            ]
        );
    }

    public function actionSend()
    {
        /**
         * @var $notifications Notifications
         */
        echo "Notification send action index started at " . date('d:m:Y H:i:s') . "\n";
        $notifications = Notifications::find()->where(['status' => 1])->andWhere(['type' => [0, 2]])->all();
        /**
         * @var Notifications $notification
         */
        foreach ($notifications as $notification) {
            echo "
            Notification: {$notification->id}, 
            from: {$notification->user_id}, 
            type: {$notification->type}, 
            time: {$notification->updated_at}, 
            diff: " . ($notification->updated_at - time()) . "
            \r";
            Notifications::send($notification);
            $notification->status = 2;
            $notification->save();
        }
        echo "Notification send action index finished at " . date('d:m:Y H:i:s') . "\n";
    }
}
