<?php namespace app\modules\api\controllers;

use app\modules\api\models\DriverLicence;
use app\modules\api\models\UploadFiles;
use app\modules\api\models\Users;
use app\modules\api\models\Vehicles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/** @property \app\modules\api\Module $module */
class UserController extends BaseController
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
                            'auth', 'sms',
                            'registration',
                            'upload-driver-licence',
                            'create-vehicle',


                            'upload-vehicle-insurance', 'upload-vehicle-registration', 'upload-vehicle-photos', 'upload-user-photo'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'auth'  => ['POST'],
                    'sms'   => ['POST'],
                    'registration' => ['PUT'],
                    'upload-driver-licence' => ['POST'],
                    'create-vehicle' => ['PUT'],

                    'upload-vehicle-insurance' => ['POST'],
                    'upload-vehicle-registration' => ['POST'],
                    'upload-vehicle-photos' => ['POST'],
                    'upload-user-photo' => ['POST'],
                ]
            ]
        ];
    }

    public function actionAuth()
    {
        $this->prepareBody();
        $this->validateBodyParams(['push_id', 'device_id', 'ostype', 'type', 'phone']);
        $this->TokenAuth(self::TOKEN_PHONE);

        /** @var \app\modules\api\models\Devices $device */
        $device = $this->Auth();
        $device = $this->module->sendSms($device, true);

        if ($device)
        {
            $this->module->data = [
                'sms' => 1,
                'user' => $device->user->toArray(),
                'token' => $device->auth_token
            ];
            $this->module->setSuccess();
            $this->module->sendResponse();
        }
        else $this->module->setError(422, '_sms', "SMS not send");
    }

    public function actionSms()
    {
        $this->prepareBody();

        $user = $this->TokenAuth(self::TOKEN_SMS);
        if ($user) $user = $this->user;

        $token = Yii::$app->security->generateRandomString();
        $this->module->data = [
            'user'  => $user->toArray(),
            'token' => $token
        ];

        $this->device->auth_token = $token;
        $this->device->save();

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionRegistration()
    {
        $this->prepareBody();

        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $data = [
            'Users' => (array) $this->body
        ];

        if (!$user->load($data)) $this->module->setError(422, 'user', "Can't load user model from data.");
        if (!$user->validate() || !$user->save())
        {
            if ($user->hasErrors())
            {
                foreach ($user->errors as $field => $error_message)
                    $this->module->setError(422, 'user.' . $field, $error_message, true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, 'user', "Can't validate user model from data.");
        }

        $this->module->data = [
            'user' => $user->toArray()
        ];
        $this->prepareScheme('users');
        $this->module->JSONValidate('user', $this->scheme);

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadDriverLicence()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        $data = $this->UploadLicenceDocument($user, DriverLicence::TYPE_LICENSE);

        $this->module->data = [
            'files' => $data['documents'],
            'user'  => $user->toArray(),
            'licence' => $data['licenses']->toArray()
        ];
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

    /*

    public function actionUploadVehicleInsurance()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        $data = $this->UploadLicenceDocument($user, DriverLicence::TYPE_INSURANCE);

        $this->module->data = [
            'files' => $data['documents'],
            'user'  => $user->toArray(),
            'licence' => $data['licenses']->toArray()
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadVehicleRegistration()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        $data = $this->UploadLicenceDocument($user, DriverLicence::TYPE_REGISTRATION);

        $this->module->data = [
            'files' => $data['documents'],
            'user'  => $user->toArray(),
            'licence' => $data['licenses']->toArray()
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadVehiclePhotos()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        $photos = [];
        foreach ($_FILES as $name => $file) $photos[$name] = $this->UploadFile($name, 'vehicle-photos/' . $user->id);

        $this->module->data = [
            'files' => $photos,
            'user'  => $user->toArray(),
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUploadUserPhoto()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        foreach ($_FILES as $name => $file)
        {
            $file = $this->UploadFile($name, 'user-photos/' . $user->id, true);
            $photos[$name] = $file['file'];
            $user->image = intval($file['file_id']);
        }

        $old_image = $user->getOldAttribute('image');
        if (!empty ($old_image) && $old_image > 0)
        {
            $image = UploadFiles::findOne(['id' => $old_image]);
            if ($image)
            {
                $file = Yii::getAlias('@webroot') . $image->file;
                if (file_exists($file)) unlink($file);
                $image->delete();
            }
        }

        if (!$user->save())
        {
            $save_errors = $user->getErrors();
            if ($save_errors && count ($save_errors) > 0)
            {
                foreach ($save_errors as $field => $error) $this->module->setError(422, $field, $error[0], true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, '_user', "Problem with file upload");
        }
        $this->user = $user;

        $this->module->data = [
            'files' => $photos,
            'user'  => $user->toArray(),
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }
    */

    /**
     * Upload documents
     * @param \app\models\User $user
     * @param int $type
     * @return array
     */
    protected function UploadLicenceDocument($user, $type = 0)
    {
        $remove_old_images = [];
        $licences = DriverLicence::findOne(['user_id' => $user->id, 'type' => $type]);
        if (!$licences) $licences = new DriverLicence([
            'user_id' => $user->id,
            'type' => $type
        ]);
        else
        {
            if (!empty ($licences->image)) $remove_old_images[] = Yii::getAlias('@webroot' . $licences->image);
            if (!empty ($licences->image2)) $remove_old_images[] = Yii::getAlias('@webroot' . $licences->image2);
        }

        $documents = [];
        switch ($type)
        {
            case DriverLicence::TYPE_LICENSE:
                foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'driver-licence/' . $user->id);
                break;

            case DriverLicence::TYPE_INSURANCE:
                foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'vehicle-insurance/' . $user->id);
                break;

            case DriverLicence::TYPE_REGISTRATION:
                foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'vehicle-registration/' . $user->id);
                break;

            default: $this->module->setError(422, 'license.type', "Unknown type");
        }

        foreach ($documents as $name => $path) switch ($name)
        {
            case 'image': $licences->$name = $path; break;
            case 'image2': $licences->$name = $path; break;
        }

        if (!$licences->save())
        {
            $save_errors = $licences->getErrors();
            if ($save_errors && count ($save_errors) > 0)
            {
                foreach ($save_errors as $field => $error) $this->module->setError(422, $field, $error[0], true, false);
                $this->module->sendResponse();
            }
            else $this->module->setError(422, '_licenses', "Problem with file upload");
        }

        // REMOVE OLD UPLOADED IMAGES
        if ($remove_old_images && count($remove_old_images) > 0)
            foreach ($remove_old_images as $file) if (file_exists($file)) unlink($file);

        return [
            'documents' => $documents,
            'licenses'  => $licences
        ];
    }
}