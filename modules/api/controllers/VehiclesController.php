<?php namespace app\modules\api\controllers;

use app\models\User;
use app\modules\api\models\Line;
use app\modules\api\models\UploadFiles;
use app\modules\api\models\VehicleBrands;
use app\modules\api\models\VehicleModels;
use app\modules\api\models\Vehicles;
use app\modules\api\models\VehicleTypes;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/** @property \app\modules\api\Module $module */
class VehiclesController extends BaseController
{
    public $modelClass = 'app\modules\api\models\RestFul';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'types', 'brands', 'models',
                            'list', 'qr',

                            'create-vehicle',
                            'update-vehicle',
                            'remove-vehicle',
                            'upload-vehicle-insurance',
                            'upload-vehicle-registration',
                            'upload-vehicle-photo',
                            'upload-vehicle-photos',
                            'delete-vehicle-photos',
                            'get-vehicle'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'list' => ['GET'],
                    'types' => ['GET'],
                    'brands' => ['GET'],
                    'models' => ['GET'],
                    'get-vehicle' => ['GET'],
                    'qr' => ['GET'],

                    'create-vehicle' => ['PUT'],
                    'update-vehicle' => ['PUT'],
                    'remove-vehicle' => ['DELETE'],
                    'upload-vehicle-insurance' => ['POST'],
                    'upload-vehicle-registration' => ['POST'],
                    'upload-vehicle-photo' => ['POST'],
                    'upload-vehicle-photos' => ['POST'],
                    'delete-vehicle-photos' => ['DELETE'],
                ]
            ]
        ];
    }

    public function actionQr($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $qr_url = false;
        $vehicle = Vehicles::findOne($id);
        if ($vehicle) {
            $qr = isset (Yii::$app->params['qr_api_url']) ? Yii::$app->params['qr_api_url'] : false;
            if (!$qr) $this->module->setError(422, '_qr', Yii::$app->mv->gt("Сервис генерации QR кода не задан", [], false));

            $data = [
                'vehicle_id' => $vehicle->id,
                'driver_id' => $vehicle->user_id
            ];

            $qr_url = str_replace(['{data}'], urlencode(json_encode($data)), $qr);
        } else $this->module->setError(422, '_vehicle', Yii::$app->mv->gt("Не найдена", [], false));

        if ($qr_url) {
            $local = '/files/vehicle-qr/' . $vehicle->id . '/';
            $path = Yii::getAlias('@webroot') . $local;
            $name = 'qr-code.png';

            if (UploadFiles::validatePath($path)) {
                $image = $path . $name;
                $file = file_put_contents($image, file_get_contents($qr_url));
                if ($file) $this->module->data['file'] = Yii::getAlias('@web') . $local . $name;
                else $this->module->setError(422, '_vehicle', Yii::$app->mv->gt("Не удалось создать изображение QR кода", [], false));
            } else $this->module->setError(422, '_vehicle', Yii::$app->mv->gt("Не удалось скопировать изображение QR кода", [], false));
        } else $this->module->setError(422, '_vehicle', Yii::$app->mv->gt("Не удалось создать QR код", [], false));

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionList()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        Vehicles::deleteAll(['user_id' => $user->id, 'status' => Vehicles::STATUS_PROCESS]);

        $_vehicles = [];
        $vehicles = Vehicles::find()->where(['user_id' => $user->id])->orderBy(['created_at' => SORT_DESC])->all();

        if ($vehicles && count($vehicles)) foreach ($vehicles as $vehicle) {
            $_vehicles[] = $vehicle->toArray();
        }

        $this->module->data = $_vehicles;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionGetVehicle($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        Vehicles::deleteAll(['user_id' => $user->id, 'status' => Vehicles::STATUS_PROCESS]);

        $vehicle = Vehicles::findOne($id);
        if ($vehicle) $vehicle = $vehicle->toArray();
        else $vehicle = null;

        $this->module->data = $vehicle;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTypes()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $with_any = false;

        switch (intval($user->type)) {
            case User::TYPE_DRIVER:
                break;
            case User::TYPE_PASSENGER:
                $with_any = true;
                break;
        }

        $this->module->data = VehicleTypes::getTypesList(true, $with_any);
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionBrands()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->module->data = VehicleBrands::getBrandsList(true);
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionModels($param1, $param2)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->module->data = VehicleModels::getModelsList($param1, $param2, true);
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCreateVehicle()
    {
        $this->prepareBody();

        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        Vehicles::deleteAll(['user_id' => $user->id, 'status' => Vehicles::STATUS_PROCESS]);

        $this->validateBodyParams(['user_id', 'vehicle_type_id', 'vehicle_brand_id', 'vehicle_model_id', 'license_plate', 'seats']);

        $type = VehicleTypes::findOne($this->body->vehicle_type_id);
        $model = VehicleModels::findOne($this->body->vehicle_model_id);
        $brand = VehicleBrands::findOne($this->body->vehicle_brand_id);

        if (!$type) $this->module->setError(422, '_type', Yii::$app->mv->gt("Не найдено", [], false));
        if (!$model) $this->module->setError(422, '_model', Yii::$app->mv->gt("Не найдено", [], false));
        if (!$brand) $this->module->setError(422, '_brand', Yii::$app->mv->gt("Не найдено", [], false));

        $vehicle = new Vehicles();
        $data['Vehicles'] = (array)$this->body;
        if (!$vehicle->load($data)) $this->module->setError(422, 'vehicle.load', Yii::$app->mv->gt("Не удалось загрузить модель", [], false));
        if (!$vehicle->validate() || !$vehicle->save()) {
            if ($vehicle->hasErrors()) {
                foreach ($vehicle->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422, 'vehicle.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422, '_vehicle', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data = [
            'vehicle' => $vehicle->toArray()
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUpdateVehicle($id)
    {
        $this->prepareBody();

        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $vehicle = Vehicles::findOne(['id' => $id]);
        if (!$vehicle) $this->module->setError(422, '_vehicle', Yii::$app->mv->gt("Не найден", [], false));

        $data = ['Vehicles' => (array)$this->body];

        if (!$vehicle->load($data)) $this->module->setError(422, 'vehicle', Yii::$app->mv->gt("Не удалось загрузить модель", [], false));

        $type = VehicleTypes::findOne($vehicle->vehicle_type_id);
        $model = VehicleModels::findOne($vehicle->vehicle_model_id);
        $brand = VehicleBrands::findOne($vehicle->vehicle_brand_id);

        if (!$type) $this->module->setError(422, '_type', Yii::$app->mv->gt("Не найдено", [], false));
        if (!$model) $this->module->setError(422, '_model', Yii::$app->mv->gt("Не найдено", [], false));
        if (!$brand) $this->module->setError(422, '_brand', Yii::$app->mv->gt("Не найдено", [], false));

        if (!$vehicle->validate() || !$vehicle->save()) {
            if ($vehicle->hasErrors()) {
                foreach ($vehicle->errors as $field => $error_message) {
                    if (is_array($error_message)) {
                        $result = '';
                        foreach ($error_message as $error) $result .= '; ' . $error;
                        $error_message = $result;
                    }
                    $this->module->setError(422, 'vehicle.' . $field, Yii::$app->mv->gt($error_message, [], false), true, false);
                }
                $this->module->sendResponse();
            } else $this->module->setError(422, '_vehicle', Yii::$app->mv->gt("Не удалось сохранить модель", [], false));
        }

        $this->module->data = [
            'vehicle' => $vehicle->toArray()
        ];
        $this->prepareScheme('vehicle');
        $this->module->JSONValidate('vehicle', $this->scheme);

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionRemoveVehicle($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $vehicle = Vehicles::findOne(['id' => $id]);

        $active_lines = Line::findAll(['vehicle_id' => $vehicle->id, 'status' => [
            Line::STATUS_QUEUE,
            Line::STATUS_WAITING,
            Line::STATUS_IN_PROGRESS
        ]]);
        if (!empty($active_lines))
            $this->module->setError(422, 'vehicle', Yii::$app->mv->gt("Автомобиль используется !", [], false));

        if (!$vehicle) $this->module->setError(422, 'vehicle', Yii::$app->mv->gt("Не найден", [], false));

        $vehicle->delete();

        $this->module->data = ['success' => true];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadVehicleInsurance($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', Yii::$app->mv->gt("Файлы не были переданы в ожидаемом формате", [], false));

        $vehicle = Vehicles::findOne(['id' => $id]);
        if (!$vehicle) $this->module->setError(422, 'vehicle', Yii::$app->mv->gt("Не найден", [], false));

        $documents = [];
        foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'vehicle-insurance/' . $user->id, true);

        if (!empty ($vehicle->insurance)) {
            $file = UploadFiles::findOne(['id' => $vehicle->insurance]);
            if ($file && !empty($file->file)) {
                $oldDocument = Yii::getAlias('@webroot') . $file->file;
                if ($oldDocument && file_exists($oldDocument)) unlink($oldDocument);
                $file->delete();
            }
        }

        $vehicle->insurance = $documents['insurance']['file_id'];
        $vehicle->save();

        $this->module->data = [
            'vehicle' => $vehicle->toArray(),
            'file' => $documents['insurance']['file']
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadVehicleRegistration($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', Yii::$app->mv->gt("Файлы не были переданы в ожидаемом формате", [], false));

        $vehicle = Vehicles::findOne(['id' => $id]);
        if (!$vehicle) $this->module->setError(422, 'vehicle', Yii::$app->mv->gt("Не найден", [], false));

        $documents = [];
        foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'vehicle-registration/' . $user->id, true);

        if (!empty ($vehicle->registration)) {
            $file = UploadFiles::findOne(['id' => $vehicle->registration]);
            if ($file && !empty($file->file)) {
                $oldDocument = Yii::getAlias('@webroot') . $file->file;
                if ($oldDocument && file_exists($oldDocument)) unlink($oldDocument);
                $file->delete();
            }
        }

        if (!empty ($vehicle->registration2)) {
            $file = UploadFiles::findOne(['id' => $vehicle->registration2]);
            if ($file && !empty($file->file)) {
                $oldDocument = Yii::getAlias('@webroot') . $file->file;
                if ($oldDocument && file_exists($oldDocument)) unlink($oldDocument);
                $file->delete();
            }
        }

        $vehicle->registration = $documents['registration']['file_id'];
        $vehicle->registration2 = $documents['registration2']['file_id'];
        $vehicle->save();

        $this->module->data = [
            'vehicle' => $vehicle->toArray(),
            'files' => $documents
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadVehiclePhoto($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', Yii::$app->mv->gt("Файлы не были переданы в ожидаемом формате", [], false));

        $finish = Yii::$app->request->getHeaders()->get('finish', false);

        $vehicle = Vehicles::findOne(['id' => $id]);
        if (!$vehicle) $this->module->setError(422, 'vehicle', Yii::$app->mv->gt("Не найден", [], false));

        $documents = [];
        foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'vehicle-photos/' . $user->id, true);

        if (!empty ($vehicle->image)) {
            $file = UploadFiles::findOne(['id' => $vehicle->image]);
            if ($file && !empty($file->file)) {
                $oldDocument = Yii::getAlias('@webroot') . $file->file;
                if ($oldDocument && file_exists($oldDocument)) unlink($oldDocument);
                $file->delete();
            }
        }

        if ($finish) $vehicle->status = Vehicles::STATUS_WAITING;

        $vehicle->image = $documents['image']['file_id'];
        $vehicle->save();

        $this->module->data = [
            'vehicle' => $vehicle->toArray(),
            'file' => $documents['image']['file']
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadVehiclePhotos($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', Yii::$app->mv->gt("Файлы не были переданы в ожидаемом формате", [], false));

        $vehicle = Vehicles::findOne(['id' => $id]);
        if (!$vehicle) $this->module->setError(422, 'vehicle', Yii::$app->mv->gt("Не найден", [], false));

        $documents = [];
        foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'vehicle-photos/' . $user->id, true);

        $saved_photos_objects = [];
        $saved_photos_objects_id = [];

        if (Yii::$app->request->post('save_photos')) {
            $saved_photos = explode(',', Yii::$app->request->post('save_photos'));
            if ($saved_photos && count($saved_photos) > 0) foreach ($saved_photos as $saved_photo) {
                $file = UploadFiles::findOne($saved_photo);
                if ($file) {
                    $saved_photos_objects[] = $file;
                    $saved_photos_objects_id[] = $file->id;
                }
            }
        }

        if (!empty ($vehicle->photos)) {
            $photos = $vehicle->getVehiclePhotos(2);
            /** @var \app\modules\api\models\UploadFiles $file */
            if ($photos && count($photos) > 0) foreach ($photos as $file) {
                if (!in_array($file->id, $saved_photos_objects_id)) $file->delete();
            }
        }

        $_photos = [];
        foreach ($documents as $file) $_photos[] = $file['file_id'];
        foreach ($saved_photos_objects as $file) $_photos[] = $file->id;
        $_photos = implode(',', $_photos);

        $vehicle->photos = (string)$_photos;
        $vehicle->save();

        $this->module->data = [
            'vehicle' => $vehicle->toArray(),
            'photos' => $documents
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionDeleteVehiclePhotos($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $vehicle = Vehicles::findOne(['id' => $id]);
        if (!$vehicle) $this->module->setError(422, 'vehicle', Yii::$app->mv->gt("Не найден", [], false));

        if (!empty ($vehicle->photos)) {
            $photos = $vehicle->getVehiclePhotos(2);
            /** @var \app\modules\api\models\UploadFiles $file */
            if ($photos && count($photos) > 0) foreach ($photos as $file) if ($file && !empty ($file->file)) $file->delete();

            $vehicle->photos = null;
            $vehicle->save();
        }

        $this->module->data['vehicle'] = $vehicle->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }
}
