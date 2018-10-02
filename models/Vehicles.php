<?php namespace app\models;

use app\modules\api\models\UploadFiles;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_vehicle".
 *
 * @property int $id
 * @property integer $user_id
 * @property integer $main
 * @property integer $status
 * @property integer $seats
 * @property string $license_plate
 * @property integer $image
 * @property string $photos
 * @property string $code
 * @property integer $generate_code
 * @property integer $insurance
 * @property integer $registration
 * @property integer $registration2
 * @property integer $weight
 * @property integer $vehicle_type_id
 * @property integer $vehicle_model_id
 * @property integer $vehicle_brand_id
 * @property float $rating
 * @property int $created_at
 * @property int $updated_at
 *
 * @property string $vehicleName
 * @property \app\models\VehicleBrand $brand
 * @property \app\models\VehicleModel $model
 * @property \app\models\VehicleType $type
 */
class Vehicles extends \yii\db\ActiveRecord
{
    const
        STATUS_ADDED = 0,
        STATUS_APPROVED = 1,
        STATUS_WAITING = 2;

    public static function tableName()
    {
        return 'user_vehicle';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'vehicle_type_id', 'vehicle_model_id', 'vehicle_brand_id', 'seats', 'license_plate'], 'required'],
            [['main', 'weight', 'seats', 'vehicle_type_id', 'vehicle_model_id', 'vehicle_brand_id', 'generate_code'], 'integer'],
            [['image', 'insurance', 'registration', 'registration2'], 'integer'],
            [['license_plate', 'photos', 'code'], 'string'],
            ['license_plate', 'unique', 'targetClass' => self::className(),
                'message' => Yii::t('app', 'This plate has already been taken.')
            ],
            [['rating'], 'number'],
            [['seats'], 'integer', 'min' => 1],
            [['main'], 'integer', 'min' => 0, 'max' => 1],
            [['weight'], 'integer', 'min' => 0],
            [['main'], 'filter', 'filter' => function ($value) {
                if ($value && intval($value) == 1) {
                    $vehicle = Vehicles::find()->andWhere([
                        'AND',
                        ['=', 'user_id', $this->user_id],
                        ['=', 'main', 1],
                        ['!=', 'id', $this->id]
                    ])->one();

                    if (!$vehicle) return 1;
                    else $this->addError('main', Yii::t('app', "У водителя уже выбран основной автомобиль"));
                }

                return 0;
            }],
            [['status'], 'default', 'value' => self::STATUS_ADDED],
            [['weight', 'main', 'rating'], 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', "ID"),
            'user_id' => Yii::t('app', "Пользователь"),
            'status' => Yii::t('app', "Статус"),
            'seats' => Yii::t('app', "Мест"),
            'license_plate' => Yii::t('app', "Номер"),
            'image' => Yii::t('app', "Фото"),
            'photos' => Yii::t('app', "Фотографии"),
            'insurance' => Yii::t('app', "Страхование"),
            'vehicle_type_id' => Yii::t('app', "Тип"),
            'vehicle_brand_id' => Yii::t('app', "Бренд"),
            'vehicle_model_id' => Yii::t('app', "Модель"),
            'created_at' => Yii::t('app', "Создано"),
            'updated_at' => Yii::t('app', "Обновлено"),
            'registration' => Yii::t('app', "Фото тех. паспорта"),
            'registration2' => Yii::t('app', "Фото тех. паспорта"),
            'rating' => Yii::t('app', "Рейтинг"),
            'main' => Yii::t('app', "Основная"),
            'generate_code' => Yii::t('app', "Сгенерировать QR код и отправить водителю на почту")
        ];
    }

    public function isFirstVehicle()
    {
        return self::findOne(['user_id' => $this->user_id]) ? false : true;
    }

    public function beforeSave($insert)
    {
        if ($this->isFirstVehicle()) $this->main = 1;

        return parent::beforeSave($insert);
    }

    public function getBrand()
    {
        return \app\modules\admin\models\VehicleBrand::findOne($this->vehicle_brand_id);
    }

    public function getModel()
    {
        return \app\modules\admin\models\VehicleModel::findOne($this->vehicle_model_id);
    }

    public function getType()
    {
        return \app\modules\admin\models\VehicleType::findOne($this->vehicle_type_id);
    }

    public function getVehicleName()
    {
        $concatenate = [];
        if ($this->brand) $concatenate[] = $this->brand->title;
        if ($this->model) $concatenate[] = $this->model->title;

        $vehicle_name = implode(' ', $concatenate);

        return $vehicle_name;
    }

    public function getVehiclePhotos($type = 1)
    {
        if (empty ($this->photos)) return null;

        $photos = explode(',', $this->photos);
        switch ($type) {
            case 1:
                $_photos = [];
                if ($photos && count($photos) > 0) foreach ($photos as $file_id) {
                    if (intval($file_id) > 0) {
                        $file = UploadFiles::findOne(intval($file_id));
                        if ($file) $_photos[] = [
                            'id' => $file->id,
                            'value' => $file->file
                        ];
                    }
                }

                if ($_photos && count($_photos) > 0) return $_photos;
                return null;
                break;

            case 2:
                $_photos = [];
                if ($photos && count($photos) > 0) foreach ($photos as $file_id) {
                    if (intval($file_id) > 0) {
                        $file = UploadFiles::findOne(intval($file_id));
                        if ($file) $_photos[] = $file;
                    }
                }

                if ($_photos && count($_photos) > 0) return $_photos;
                return null;
                break;
        }

        return null;
    }
}
