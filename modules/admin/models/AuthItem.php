<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 */
class AuthItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'description'], 'required'],
			['name', 'unique', 'targetClass' => self::className(), 'message' => 'Данная роль уже занята'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::$app->mv->gt('Role key', [], false),
            'type' => Yii::$app->mv->gt('Type', [], false),
            'description' => Yii::$app->mv->gt('Role Name', [], false),
            'rule_name' => Yii::$app->mv->gt('Rule Name', [], false),
            'data' => Yii::$app->mv->gt('Data', [], false),
            'created_at' => Yii::$app->mv->gt('Created', [], false),
            'updated_at' => Yii::$app->mv->gt('Updated', [], false),
        ];
    }

	public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    public static function fromFilterValue(){
        return ArrayHelper::map(
                self::find()->orderBy(['type' => SORT_ASC])->all()
                ,'name', 'description'
            );
    }
}
