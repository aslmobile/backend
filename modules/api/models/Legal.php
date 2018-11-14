<?php namespace app\modules\api\models;

class Legal extends \app\models\Legal
{
    public function afterFind()
    {
        $this->content = json_decode($this->content);
    }
}
