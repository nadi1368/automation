<?php

namespace hesabro\automation\controllers;

use Yii;
use hesabro\automation\Module;
use hesabro\helpers\traits\AjaxValidationTrait;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class AuSettingsController
 * @package backend\controllers
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AuSettingsController extends Controller
{
    use AjaxValidationTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['settings-account/index', 'setting/index', 'superadmin'],
                    ],
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new (Module::getInstance()->settingsSearch);
        $searchModel->status = Module::getInstance()->settings::STATUS_ACTIVE;
        $searchModel->category = Module::getInstance()->settingsCategory;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionChangeValue($id)
    {
        $result = [
            'success' => false,
            'msg' => Module::t("module", "Error In Save Info")
        ];
        $model = $this->findModel($id);
        $model->setScenario(Module::getInstance()->settings::SCENARIO_CHANGE_VALUE);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Module::t("module", "Item Updated")
                    ];
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
            return $this->asJson($result);
        } else {
            if ($model->field == 'itemMultiple') {
                $model->client_value = explode(',', $model->client_value);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('change-value', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Settings model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Settings the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Module::getInstance()->settings::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }


    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }

}
