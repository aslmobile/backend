<?php

namespace app\modules\admin\controllers;

use app\components\Controller;
use app\modules\admin\models\Bid;
use app\modules\admin\models\City;
use app\modules\admin\models\Countries;
use app\modules\admin\models\Regions;
use app\modules\admin\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * DefaultController implements the CRUD actions for User model.
 */
class DefaultController extends Controller
{
    public $layout = "./sidebar";

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'index', 'create', 'update', 'view', 'get-statistic',
                            'select-countries', 'select-regions', 'select-cities'
                        ],
                        'allow' => true, 'roles' => ['admin', 'moderator']
                    ],
                    [
                        'actions' => ['delete', 'delete-group'],
                        'allow' => true, 'roles' => ['admin']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post']
                ]
            ]
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSelectCountries($q = null, $id = null)
    {
        if (!empty($id)) $id = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q))
        {
            $out['results'] = Countries::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        }
        elseif (is_array($id))
        {
            $out['results'] = Countries::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    public function actionSelectRegions($q = null, $id = null)
    {
        if (!empty($id)) $id = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q))
        {
            $out['results'] = Regions::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        }
        elseif (is_array($id))
        {
            $out['results'] = Regions::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    public function actionSelectCities($q = null, $id = null)
    {
        if (!empty($id)) $id = explode(',', $id);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q))
        {
            $out['results'] = City::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere([
                    'OR',
                    ['like', 'title', $q]
                ])
                ->limit(10)->asArray()->all();
        }
        elseif (is_array($id))
        {
            $out['results'] = City::find()
                ->select(['id', 'text' => 'title'])
                ->andWhere(['in', 'id', $id])->asArray()->all();
        }

        return $out;
    }

    public function actionGetStatistic(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        if (\Yii::$app->request->isAjax){
            $interval = \Yii::$app->request->get('interval', 7);
            $users = User::getUserRegistrationStatisticData($interval);
            foreach (array_keys($users) as $date) {
                $response[] = [
                    'date' => $date,
                    'users' => array_key_exists($date, $users) ? $users[$date] : 0,
                ];
            }
        }
        return $response;
    }
}
