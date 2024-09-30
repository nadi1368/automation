<?php

namespace hesabro\automation\controllers;

use Yii;
use hesabro\automation\models\AuClientGroup;
use hesabro\automation\models\AuClientGroupSearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use hesabro\helpers\traits\AjaxValidationTrait;

/**
 * AuClientGroupController implements the CRUD actions for AuClientGroup model.
 */
class AuClientGroupController extends Controller
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
                            'roles' => ['automation/au-client-group/view', 'superadmin'],
                            'actions' => ['index', 'view']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['automation/au-client-group/action', 'superadmin'],
                            'actions' => ['create', 'update', 'set-in-active', 'set-active', 'delete']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all AuClientGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuClientGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new AuClientGroup();
        $result = [
            'success' => false,
            'msg' => Yii::t("app", "Error In Save Info")
        ];
        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    if ($flag) {
                        $result = [
                            'success' => true,
                            'msg' => Yii::t("app", "Item Created")
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
        }else {
            $model->loadDefaultValues();
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if(!$model->canUpdate())
        {
            throw new BadRequestHttpException(Yii::t("app", "It is not possible to perform this operation"));
        }
        $result = [
            'success' => false,
            'msg' => Yii::t("app", "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if ($flag) {
                    $result = [
                        'success' => true,
                        'msg' => Yii::t("app", "Item Updated")
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
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSetActive($id)
    {
        $model = $this->findModel($id);

        if ($model->canActive()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = AuClientGroup::STATUS_ACTIVE;
                $flag = $model->save(false);
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Yii::t("app", "Item Updated")
                    ];
                } else {
                    $transaction->rollBack();
                    $result = [
                        'status' => false,
                        'message' => Yii::t("app", "Error In Save Info")
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
                'message' => Yii::t("app", "It is not possible to perform this operation")
            ];
        }
        return $this->asJson($result);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSetInActive($id)
    {
        $model = $this->findModel($id);
        if ($model->canInActive()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = AuClientGroup::STATUS_INACTIVE;
                $flag = $model->save(false);
                if ($flag) {
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Yii::t("app", "Item Updated")
                    ];
                } else {
                    $transaction->rollBack();
                    $result = [
                        'status' => false,
                        'message' => Yii::t("app", "Error In Save Info")
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
                'message' => Yii::t("app", "It is not possible to perform this operation")
            ];
        }

        return $this->asJson($result);
    }


    /**
     * Finds the AuClientGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id آیدی
     * @return AuClientGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuClientGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
