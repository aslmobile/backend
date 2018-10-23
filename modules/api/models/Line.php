<?php namespace app\modules\api\models;

class Line extends \app\models\Line
{

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

}
