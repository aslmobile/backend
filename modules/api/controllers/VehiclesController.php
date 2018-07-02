<?php namespace app\modules\api\controllers;

use app\modules\api\models\VehicleBrands;
use app\modules\api\models\VehicleModels;
use app\modules\api\models\Vehicles;
use app\modules\api\models\VehicleTypes;
use app\modules\api\models\UploadFiles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/** @property \app\modules\api\Module $module */
class VehiclesController extends BaseController
{
    public $modelClass = 'app\modules\api\models\RestFul';

    public function init()
    {
        parent::init();

        $authHeader = Yii::$app->request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) $this->token = $matches[1];
        else $this->module->setError(403, '_token', "Token required!");
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'types', 'brands', 'models',
                            'list',

                            'create-vehicle',
                            'update-vehicle',
                            'remove-vehicle',
                            'upload-vehicle-insurance',
                            'upload-vehicle-registration',
                            'upload-vehicle-photo',
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'list' => ['GET'],
                    'types'  => ['GET'],
                    'brands'  => ['GET'],
                    'models'  => ['GET'],

                    'create-vehicle' => ['PUT'],
                    'update-vehicle' => ['PUT'],
                    'remove-vehicle' => ['DELETE'],
                    'upload-vehicle-insurance' => ['POST'],
                    'upload-vehicle-registration' => ['POST'],
                    'upload-vehicle-photo' => ['POST'],
                ]
            ]
        ];
    }

    public function actionList()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $_vehicles = [];
        $vehicles = Vehicles::find()->where(['user_id' => $user->id])->orderBy(['created_at' => SORT_DESC])->all();

        if ($vehicles && count ($vehicles)) foreach ($vehicles as $vehicle)
        {
            $_vehicles[] = $vehicle->toArray();
        }

        $this->module->data = $_vehicles;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTypes()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $types = VehicleTypes::getTypesList(true);

        $this->module->data = $types;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionBrands($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $brands = VehicleBrands::getBrandsList($id, true);
        if (!$brands || empty ($brands) || count ($brands) == 0) $brands = null;
        $this->module->data = $brands;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionModels($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $models = VehicleModels::getModelsList($id, true);

        $this->module->data = $models;
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionCreateVehicle()
    {
        $this->prepareBody();

        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->validateBodyParams(['user_id', 'vehicle_type_id', 'vehicle_brand_id', 'vehicle_model_id', 'license_plate', 'seats']);

        $vehicle = new Vehicles();
        $data['Vehicles'] = (array) $this->body;
        if (!$vehicle->load($data)) $this->module->setError(422, 'vehicle.load', "Can't load vehicle model");
        if (!$vehicle->validate() || !$vehicle->save())
        {
            if ($vehicle->hasErrors())
            {
                foreach ($vehicle->errors as $field => $error)
                    $this->module->setError(422, 'vehicle.' . $field, $error, true, false);

                $this->module->sendResponse();
            }
            else $this->module->setError(422, 'vehicle.save', "Can't save vehicle model");
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
        if (!$vehicle) $this->module->setError(422, 'vehicle', "Not Found");

        $data = [
            'Vehicles' => (array) $this->body
        ];

        if (!$vehicle->load($data)) $this->module->setError(422, 'vehicle', "Can't load vehicle model from data.");
        if (!$vehicle->validate() || !$vehicle->save())
        {
            if ($vehicle->hasErrors())
            {
                foreach ($vehicle->errors as $field => $error_message)
                    $this->module->setError(422, 'vehicle.' . $field, $error_message, true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, 'vehicle', "Can't validate vehicle model from data.");
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
        if (!$vehicle) $this->module->setError(422, 'vehicle', "Not Found");

        $vehicle->delete();

        $this->module->data = ['success' => true];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadVehicleInsurance($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        $vehicle = Vehicles::findOne(['id' => $id]);
        if (!$vehicle) $this->module->setError(422, 'vehicle', "Not Found");

        $documents = [];
        foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'vehicle-insurance/' . $user->id, true);

        if (!empty ($vehicle->insurance))
        {
            $file = UploadFiles::findOne(['id' => $vehicle->insurance]);
            if ($file && !empty($file->file))
            {
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

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        $vehicle = Vehicles::findOne(['id' => $id]);
        if (!$vehicle) $this->module->setError(422, 'vehicle', "Not Found");

        $documents = [];
        foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'vehicle-registration/' . $user->id, true);

        if (!empty ($vehicle->registration))
        {
            $file = UploadFiles::findOne(['id' => $vehicle->registration]);
            if ($file && !empty($file->file))
            {
                $oldDocument = Yii::getAlias('@webroot') . $file->file;
                if ($oldDocument && file_exists($oldDocument)) unlink($oldDocument);
                $file->delete();
            }
        }

        if (!empty ($vehicle->registration2))
        {
            $file = UploadFiles::findOne(['id' => $vehicle->registration2]);
            if ($file && !empty($file->file))
            {
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

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        $vehicle = Vehicles::findOne(['id' => $id]);
        if (!$vehicle) $this->module->setError(422, 'vehicle', "Not Found");

        $documents = [];
        foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'vehicle-photos/' . $user->id, true);

        if (!empty ($vehicle->image))
        {
            $file = UploadFiles::findOne(['id' => $vehicle->image]);
            if ($file && !empty($file->file))
            {
                $oldDocument = Yii::getAlias('@webroot') . $file->file;
                if ($oldDocument && file_exists($oldDocument)) unlink($oldDocument);
                $file->delete();
            }
        }

        $vehicle->image = $documents['image']['file_id'];
        $vehicle->save();

        $this->module->data = [
            'vehicle' => $vehicle->toArray(),
            'file' => $documents['image']['file']
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }
}