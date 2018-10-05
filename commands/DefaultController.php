<?php

namespace app\commands;

use app\components\ConsoleController;
use app\models\Line;
use yii\base\Module;

class DefaultController extends ConsoleController
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
        $lines = Line::find()->all();
        /** @var Line $line */
        foreach ($lines as $line){
            if(!empty($line->vehicle)){
                $line->vehicle_type_id = $line->vehicle->vehicle_type_id;
                $line->update(false);
            }
        }
    }

}
