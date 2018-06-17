<?php

namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;

class Category extends \app\models\Category {
    public static function fromFilterValue($is_select = true){
        $default = [];
//        if($is_select){
//            $default[0] = Yii::$app->mv->gt('Not selected',[],false);
//        }
        return ArrayHelper::merge(
            $default,
            ArrayHelper::map(
                self::find()->orderBy(['title' => SORT_ASC])->all()
                ,'id', 'title'
            )
        );
    }
}