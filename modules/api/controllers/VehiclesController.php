<?php namespace app\modules\api\controllers;

use app\modules\api\models\VehicleBrands;
use app\modules\api\models\VehicleModels;
use app\modules\api\models\VehicleTypes;
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
                            'types', 'brands', 'models'
                        ],
                        'allow' => true
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'types'  => ['GET'],
                    'brands'  => ['GET'],
                    'models'  => ['GET'],
                ]
            ]
        ];
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

    /**
     * @param $id integer // Type ID
     */
    public function actionBrands($id)
    {
        $user = $this->TokenAuth(self::TOKEN);
        if ($user) $user = $this->user;

        $brands = VehicleBrands::getBrandsList($id, true);

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
}