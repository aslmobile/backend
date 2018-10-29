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

    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionSend()
    {
        /**
         * @var $notifications Notifications
         */
        echo "Notification send action index started at " . date('d:m:Y H:i:s') . "\n";

        $notifications = Notifications::find()->where([
            'OR',
            ['=', 'status', Notifications::STATUS_NEW],
            ['AND', ['=', 'status', Notifications::STATUS_SCHEDULED], ['!=', 'time', 0], ['>=', 'time', time() - 3600]]
        ])->all();

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
            $notification->status = Notifications::STATUS_WAITING;
            $notification->update();
        }
        echo "Notification send action index finished at " . date('d:m:Y H:i:s') . "\n";
    }

    public function actionTest()
    {
        $push = \Yii::$app->push;
        $notification = Notifications::findOne(2);
//        $push->ios()->send('8a27747526c52cc5a66e920981ee069baa55ae4cd769b86125a1fe39494c0788', ['aps' => [
//            'alert' => $notification->text . ': ' . $notification->title,
//            'sound' => 'default',
//            'notification_id' => $notification->id,
//            'type' => $notification->type,
//        ]]);
        $push->firebase()->send('fFmvUgR_p0E:APA91bGZRVq90J1SfGBqTDt1e0JQMAVnlkD5nO6vFbAM4XTPdr7FooY1R-G5fAJt0F82ijnsjnKp25344feDxuP_7yt7gaSJmp7VridE9L-rPcZWQQwrY_JuHu3W1poibvZXmZIbgiC-',
            [
//                'notification' => [
//                    'body' => $notification->text,
//                    'title' => $notification->title,
//                    'sound' => 'default',
//                ],
                'priority' => 'high',
                'data' => [
                    'title' => $notification->title,
                    'body' => $notification->text,
                    'time' => $notification->time,
                    'type' => $notification->type,
                ],
            ], 1);
    }
}
