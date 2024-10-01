<?php

namespace hesabro\automation\controllers;

use backend\modules\master\models\Client;
use backend\modules\master\models\ClientSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class AuClientController
 * @package hesabro\automation\controllers
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AuClientController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['automation/index', 'superadmin'],
                            'matchCallback' => function ($rule, $action) {
                                return \Yii::$app->client->identity->master;
                            }
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->searchMy(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null && $model->parent_id == \Yii::$app->client->id) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
