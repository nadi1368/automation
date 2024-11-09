<?php

namespace hesabro\automation\controllers;

use hesabro\automation\models\AuWorkFlowStep;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use hesabro\automation\models\AuLetter;
use hesabro\automation\Module;
use hesabro\automation\models\AuWorkFlow;
use hesabro\automation\models\AuWorkFlowSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;
use yii\web\BadRequestHttpException;

/**
 * AuWorkFlowController implements the CRUD actions for AuWorkFlow model.
 */
class AuWorkFlowController extends Controller
{
    use AjaxValidationTrait;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['automation/au-work-flow/manage', 'superadmin'],
                            'actions' => ['index', 'view', 'create', 'update', 'delete']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all AuWorkFlow models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuWorkFlowSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $type
     * @return mixed
     */
    public function actionView($type)
    {
        return $this->render('view', [
            'type' => $type,
            'title' => $this->findModelLetterType($type),
            'items'=>AuWorkFlow::find()->byLetterType($type)->all()
        ]);
    }

    /**
     * @param $type
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuWorkFlow();

        $result = [
            'success' => false,
            'msg' => Yii::t("app", "Error In Save Info")
        ];

        if ($this->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->steps = AuWorkFlowStep::createMultiple(AuWorkFlowStep::class);
            $valid = AuWorkFlowStep::loadMultiple($model->steps, Yii::$app->request->post());
            $valid = $valid && AuWorkFlowStep::validateMultiple($model->steps);
            $valid = $valid && $model->validate();

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    if ($flag) {
                        $result = [
                            'success' => true,
                            'msg' => Module::t('module', "Item Created")
                        ];
                        $transaction->commit();
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
                }
                return $this->asJson($result);
            }
        }

        !$model->hasErrors() && $model->loadDefaultValues();
        $model->steps = [new AuWorkFlowStep()];

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if(!$model->canUpdate())
        {
            throw new BadRequestHttpException(Module::t('module', "It is not possible to perform this operation"));
        }
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post())) {
            $model->steps = AuWorkFlowStep::createMultiple(AuWorkFlowStep::class);
            $valid = AuWorkFlowStep::loadMultiple($model->steps, Yii::$app->request->post());
            $valid = $valid && AuWorkFlowStep::validateMultiple($model->steps);
            $valid = $valid && $model->validate();

            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    if ($flag) {
                        $result = [
                            'success' => true,
                            'msg' => Module::t('module', "Item Updated")
                        ];
                        $transaction->commit();
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id.'/'.Yii::$app->controller->action->id);
                }
                return $this->asJson($result);
            }
        }

        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->canDelete()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $flag = $model->softDelete();
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Module::t('module', "Item Deleted")
                    ];
                } else {
                    $transaction->rollBack();
                    $result = [
                        'status' => false,
                        'message' => Module::t('module', "Error In Save Info")
                    ];
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $result = [
                    'status' => false,
                    'message' => $e->getMessage()
                ];
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        } else {
            $result = [
                'status' => false,
                'message' => Module::t('module', "It is not possible to perform this operation")
            ];
        }
        return $this->asJson($result);
    }
    /**
     * Finds the AuWorkFlow model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id آیدی
     * @return AuWorkFlow the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuWorkFlow::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $type
     * @return string
     */
    protected function findModelLetterType($type)
    {
        if (array_key_exists($type, AuLetter::itemAlias('Type'))) {
            return AuLetter::itemAlias('Type', $type);
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
