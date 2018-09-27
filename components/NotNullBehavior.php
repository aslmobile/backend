<?php

namespace app\components;

use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class NotNullBehavior extends AttributeBehavior
{

    /**  @inheritdoc */
    public function events()
    {
        return [ActiveRecord::EVENT_AFTER_FIND => 'afterFind'];
    }

    /**  Handle 'afterFind' event of the owner */
    public function afterFind()
    {

        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        $schema = $owner::getTableSchema();
        $columns = $schema->columns;

        foreach ($owner->attributes as $attribute => $value) {

            if (is_null($value)) switch ($columns[$attribute]->phpType) {
                case 'integer':
                    $owner->$attribute = intval($value);
                    break;
                case 'double':
                    $owner->$attribute = doubleval($value);
                    break;
                case 'string':
                    $owner->$attribute = strval($value);
                    break;
                default:
                    $owner->$attribute = intval($value);
            };

        }
    }

}
