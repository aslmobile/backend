<?php

namespace app\modules\admin\controllers;

use app\components\Controller;
use Yii;
use app\models\StaticContent;
use app\modules\admin\models\StaticContentSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * StaticContentController implements the CRUD actions for StaticContent model.
 */
class StaticContentController extends Controller
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

    /**
     * Lists all StaticContent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = $this->findModel(1);
        if ($model === null) {
            $model = new StaticContent();
            //throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::$app->mv->gt('Changes saved',[],false));
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }

    
    /**
    * Finds the StaticContent model based on its primary key value.
    * If the model is not found, a 404 HTTP exception will be thrown.
    * @param integer $id
    * @return StaticContent the loaded model
    * @throws NotFoundHttpException if the model cannot be found
    */
    protected function findModel($id, $ml = true)
        {
        if ($ml) {
            $model =  StaticContent::find()->where('id = :id', [':id' => $id])->multilingual()->one();
        } else {
            $model =  StaticContent::findOne($id);
        }

        return $model;
    }
}
