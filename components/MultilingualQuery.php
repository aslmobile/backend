<?php
namespace app\components;

use yii\db\ActiveQuery;

class MultilingualQuery extends ActiveQuery
{
    use MultilingualTrait;
}