<?php namespace app\modules\api\controllers;

use app\modules\api\models\DriverLicence;
use app\modules\api\models\Trip;
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
                            'upload-user-photo',
                            'update-profile',
                            'trips'
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
                    'upload-user-photo' => ['POST'],
                    'update-profile' => ['POST'],
                    'trips' => ['GET'],
                ]
            ]
        ];
    }

    public function actionAuth()
    {
        $this->prepareBody();
        $this->validateBodyParams(['type', 'phone']);
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

    public function actionSettings()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();
        $this->validateBodyParams(['notifications']);

        $this->device->notifications = intval($this->body->notifications) == 1 ? 1 : 0;
        $this->device->save();

        $this->module->data['device'] = $this->device->toArray();
        $this->module->data['user'] = $this->user->toArray();
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionUpdateProfile($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $this->prepareBody();

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

        $this->module->data['user'] = $user->toArray();
        $this->prepareScheme('users');
        $this->module->JSONValidate('user', $this->scheme);

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

    public function actionUploadUserPhoto()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        if (empty ($_FILES)) $this->module->setError(411, '_files', 'Empty');

        $documents = [];
        foreach ($_FILES as $name => $file) $documents[$name] = $this->UploadFile($name, 'user-photos/' . $user->id, true);

        if (!empty ($user->image))
        {
            $file = UploadFiles::findOne(['id' => $user->image]);
            if ($file && !empty($file->file))
            {
                $oldDocument = Yii::getAlias('@webroot') . $file->file;
                if ($oldDocument && file_exists($oldDocument)) unlink($oldDocument);
                $file->delete();
            }
        }

        $user->image = $documents['image']['file_id'];
        $user->save();

        $this->module->data = [
            'user' => $user->toArray(),
            'file' => $documents['image']['file']
        ];
        $this->module->setSuccess();
        $this->module->sendResponse();
    }

    public function actionTrips()
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        /** @var \app\modules\api\models\Trip $trip */
        $trips = Trip::find()->where(['user_id' => $user->id])->all();
        if ($trips && count($trips)) foreach ($trips as $trip)
        {
            $this->module->data['trips'][] = $trip->toArray();
        }

        $this->module->setSuccess();
        $this->module->sendResponse();
    }

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