<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components;

use yii\base\InvalidCallException;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;


class UserBehavior extends AttributeBehavior
{

    public $createdAtAttribute = 'created_by';
    public $updatedAtAttribute = 'updated_by';
    public $value;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdAtAttribute, $this->updatedAtAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedAtAttribute,
            ];
        }
    }

    /**
     * {@inheritdoc}
     *
     * In case, when the [[value]] is `null`, the result of the PHP function [time()](http://php.net/manual/en/function.time.php)
     * will be used as value.
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return \Yii::$app->user->id;
        }

        return parent::getValue($event);
    }

    /**
     * Updates a timestamp attribute to the current timestamp.
     *
     * ```php
     * $model->touch('lastVisit');
     * ```
     * @param string $attribute the name of the attribute to update.
     * @throws InvalidCallException if owner is a new record (since version 2.0.6).
     */
    public function touch($attribute)
    {
        /* @var $owner BaseActiveRecord */
        $owner = $this->owner;
        if ($owner->getIsNewRecord()) {
            throw new InvalidCallException('Updating the timestamp is not possible on a new record.');
        }
        $owner->updateAttributes(array_fill_keys((array) $attribute, $this->getValue(null)));
    }
}
