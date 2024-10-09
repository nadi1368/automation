<?php

namespace hesabro\automation\controllers;

use hesabro\automation\events\AuSignatureEvent;
use hesabro\automation\Module;
use Yii;
use hesabro\automation\models\AuSignature;
use hesabro\automation\models\AuSignatureSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * AuSignatureController implements the CRUD actions for AuSignature model.
 */
class AuSignatureController extends Controller
{
    public const EVENT_BEFORE_CREATE = 'beforeCreate';

    public const EVENT_AFTER_CREATE = 'afterCreate';

    public const EVENT_BEFORE_UPDATE = 'beforeUpdate';

    public const EVENT_AFTER_UPDATE = 'afterUpdate';

    public const EVENT_BEFORE_DELETE = 'beforeDelete';

    public const EVENT_AFTER_DELETE = 'afterDelete';

    public const EVENT_BEFORE_SET_ACTIVE = 'beforeSetActive';

    public const EVENT_AFTER_SET_ACTIVE = 'afterSetActive';

    public const EVENT_BEFORE_SET_INACTIVE = 'beforeSetInactive';

    public const EVENT_AFTER_SET_INACTIVE = 'afterSetInactive';

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
                'class' => AccessControl::class,
                'rules' =>
                    [
                        [
                            'allow' => true,
                            'roles' => ['automation/au-signature/manage', 'superadmin'],
                            'actions' => ['index', 'view', 'create', 'update', 'set-in-active', 'set-active', 'delete']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all AuSignature models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuSignatureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new AuSignature model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new AuSignature(['scenario' => AuSignature::SCENARIO_CREATE]);

        if ($model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $this->trigger(self::EVENT_BEFORE_CREATE, AuSignatureEvent::create($model));
                $flag = $model->save();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_CREATE, AuSignatureEvent::create($model));
                    $transaction->commit();
                    $this->flash("success", Module::t('module', 'Item Created'));
                    return $this->redirect(['index']);
                } else {
                    $transaction->rollBack();
                    $this->flash("warning", Module::t('module', "Error In Save Info"));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->flash('warning', $e->getMessage());
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AuSignature model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id آیدی
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(AuSignature::SCENARIO_UPDATE);
        if (!$model->canUpdate()) {
            $this->flash('danger', Module::t('module', "Can Not Update"));
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $this->trigger(self::EVENT_BEFORE_UPDATE, AuSignatureEvent::create($model));
                $flag = $model->save();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_UPDATE, AuSignatureEvent::create($model));
                    $transaction->commit();
                    $this->flash("success", Module::t('module', 'Item Updated'));
                    return $this->redirect(['index']);
                } else {
                    $transaction->rollBack();
                    $this->flash("warning", Module::t('module', "Error In Save Info"));
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                $this->flash('warning', $e->getMessage());
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->canDelete()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $this->trigger(self::EVENT_BEFORE_DELETE, AuSignatureEvent::create($model));
                $flag = $model->softDelete();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_DELETE, AuSignatureEvent::create($model));
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
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionSetActive($id)
    {
        $model = $this->findModel($id);

        if ($model->canActive()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $this->trigger(self::EVENT_BEFORE_SET_ACTIVE, AuSignatureEvent::create($model));
                $model->status = AuSignature::STATUS_ACTIVE;
                $flag = $model->save(false);
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_SET_ACTIVE, AuSignatureEvent::create($model));
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Module::t('module', "Item Updated")
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
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionSetInActive($id)
    {
        $model = $this->findModel($id);
        if ($model->canInActive()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $this->trigger(self::EVENT_BEFORE_SET_INACTIVE, AuSignatureEvent::create($model));
                $model->status = AuSignature::STATUS_INACTIVE;
                $flag = $model->save(false);
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_SET_INACTIVE, AuSignatureEvent::create($model));
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Module::t('module', "Item Updated")
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
     * Finds the AuSignature model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id آیدی
     * @return AuSignature the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuSignature::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
