<?php

namespace hesabro\automation\controllers;

use hesabro\automation\events\AuPrintLayoutEvent;
use hesabro\automation\Module;
use Yii;
use hesabro\automation\models\AuPrintLayout;
use hesabro\automation\models\AuPrintLayoutSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * AuPrintLayoutController implements the CRUD actions for AuPrintLayout model.
 */
class AuPrintLayoutController extends Controller
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
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' =>
//                [
//                    [
//                        'allow' => true,
//                        'roles' => ['AuPrintLayout/index', 'superadmin'],
//                        'actions' => ['index']
//                    ],
//                    [
//                        'allow' => true,
//                        'roles' => ['AuPrintLayout/create', 'superadmin'],
//                        'actions' => ['create']
//                    ],
//                    [
//                        'allow' => true,
//                        'roles' => ['AuPrintLayout/update', 'superadmin'],
//                        'actions' => ['update', 'set-in-active', 'set-active']
//                    ],
//                    [
//                        'allow' => true,
//                        'roles' => ['AuPrintLayout/delete', 'superadmin'],
//                        'actions' => ['delete']
//                    ],
//                    [
//                        'allow' => true,
//                        'roles' => ['AuPrintLayout/view', 'superadmin'],
//                        'actions' => ['view']
//                    ],
//                ]
//            ]
        ];
    }

    /**
     * Lists all AuPrintLayout models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuPrintLayoutSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new AuPrintLayout model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new AuPrintLayout();
        $model->setScenario(AuPrintLayout::SCENARIO_CREATE);
        $this->trigger(self::EVENT_BEFORE_CREATE, AuPrintLayoutEvent::create($model));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->trigger(self::EVENT_AFTER_CREATE, AuPrintLayoutEvent::create($model));
            $this->flash('success', Module::t('module', "Item Created"));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AuPrintLayout model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id آیدی
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(AuPrintLayout::SCENARIO_UPDATE);
        if (!$model->canUpdate()) {
            $this->flash('danger', Module::t('module', "Can Not Update"));
            return $this->redirect(['index']);
        }
        $this->trigger(self::EVENT_BEFORE_UPDATE, AuPrintLayoutEvent::create($model));
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->trigger(self::EVENT_AFTER_UPDATE, AuPrintLayoutEvent::create($model));
            $this->flash('success', Module::t('module', "Item Updated"));
            return $this->redirect(['index']);
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
                $this->trigger(self::EVENT_BEFORE_DELETE, AuPrintLayoutEvent::create($model));
                $flag = $model->softDelete();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_DELETE, AuPrintLayoutEvent::create($model));
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
                $this->trigger(self::EVENT_BEFORE_SET_ACTIVE, AuPrintLayoutEvent::create($model));
                $model->status = AuPrintLayout::STATUS_ACTIVE;
                $flag = $model->save(false);
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_SET_ACTIVE, AuPrintLayoutEvent::create($model));
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
                $this->trigger(self::EVENT_BEFORE_SET_INACTIVE, AuPrintLayoutEvent::create($model));
                $model->status = AuPrintLayout::STATUS_INACTIVE;
                $flag = $model->save(false);
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_SET_INACTIVE, AuPrintLayoutEvent::create($model));
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
     * Finds the AuPrintLayout model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id آیدی
     * @return AuPrintLayout the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuPrintLayout::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
