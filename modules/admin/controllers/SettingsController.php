<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\Settings;
use app\components\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class SettingsController extends Controller
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
        ];
    }

    public function actionIndex()
    {
		//$model = Settings::find()->where('id = 1')->multilingual()->one();
        $model = $this->findModel(1);
		if ($model === null) {
			throw new NotFoundHttpException('The requested page does not exist.');
		}

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Изменения сохранены',[],false));
		}
		
        return $this->render('index', [
			'model' => $model
		]);
    }

    protected function findModel($id, $ml = true)
    {
        if ($ml) {
            $model = Settings::find()->where('id = :id', [':id' => $id])->multilingual()->one();
        } else {
            $model = Settings::findOne($id);
        }

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::$app->mv->gt('Page not found',[],0));
        }
    }
}
