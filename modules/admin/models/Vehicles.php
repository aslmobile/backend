<?php namespace app\modules\admin\models;

use Da\QrCode\QrCode;
use Yii;
use yii\helpers\BaseFileHelper;
use yii\helpers\Url;

/**
 * Vehicles represents the model `\app\models\Vehicles`.
 */
class Vehicles extends \app\models\Vehicles
{

    public $storePath = '@app/web/files/vehicle-codes';
    public $relPath = '/files/vehicle-codes';

    public $generate_code;

    public function getModelTitle()
    {
        return Yii::t('app', "Автомобиль");
    }

    /**
     * Model Special Content
     * @return string
     */
    public function getSc()
    {
        return 'vehicle';
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_ADDED => Yii::t('app', "Добавлена"),
            self::STATUS_APPROVED => Yii::t('app', "Одобрена"),
            self::STATUS_WAITING => Yii::t('app', "Ждет одобрения")
        ];
    }

    public function getUser()
    {
        return User::findOne(['id' => $this->user_id]);
    }

    public function getStorePath()
    {
        return Yii::getAlias($this->storePath);
    }

    public function writeQrCode()
    {
        $storePath = $this->getStorePath() . DIRECTORY_SEPARATOR . $this->id;
        BaseFileHelper::createDirectory($storePath, 0777, true);

        $qrCode = (new QrCode($this->id))->setSize(500)->setMargin(5);
        $qrCode->writeFile($storePath . '/code.png');

        $user = $this->user;

        if (!empty($user)) {
            Yii::$app->mailer->compose('driverQr', ['user' => $user])
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                ->setTo($user->email)
                ->setSubject('Email confirmation from ' . Yii::$app->name)
                ->attach($storePath . '/code.png')
                ->send();
        }

        $this->code = Url::to($this->relPath . '/' . $this->id . '/code.png');
        $this->update();
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }
}
