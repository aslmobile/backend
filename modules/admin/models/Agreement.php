<?php namespace app\modules\admin\models;

/**
 * Vehicles represents the model `\app\models\Agreement`.
 */
class Agreement extends \app\models\Agreement
{
    public function afterFind()
    {
        $this->content = json_decode($this->content);
    }
}
