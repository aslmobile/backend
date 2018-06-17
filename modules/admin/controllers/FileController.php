<?php

namespace app\modules\admin\controllers;

use Yii;
use app\components\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class FileController extends Controller
{
	public $layout = "./sidebar";
	
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['admin', 'moderator'],
                    ],
                ],
            ],		
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
