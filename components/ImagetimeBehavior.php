<?php
/**
 * Created by PhpStorm.
 * User: keiZ
 * Date: 08.10.2016
 * Time: 16:26
 */
namespace app\components;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class ImagetimeBehavior extends Behavior
{

    public $images;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
        ];
    }

    public function beforeSave()
    {
        $time = time();
        foreach($this->images as $f){
            if($this->owner->$f) {
                $url = explode('?', $this->owner->$f);
                $url = current($url) . '?' . $time;
                $this->owner->setAttribute($f, $url);
            }
        }

    }

    public function afterFind()
    {

    }

}