<?php namespace app\modules\api\models;

class Faq extends \app\models\Faq
{
    public function afterFind()
    {
        $this->content = json_decode($this->content);
    }
}
