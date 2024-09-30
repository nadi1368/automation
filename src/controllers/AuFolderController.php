<?php

namespace hesabro\automation\controllers;

use hesabro\automation\events\AuFolderEvent;
use hesabro\automation\Module;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use hesabro\automation\models\AuFolder;
use hesabro\automation\models\AuFolderSearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * AFolderController implements the CRUD actions for AuFolder model.
 */
class AuFolderController extends Controller
{
    use AjaxValidationTrait;

    const EVENT_BEFORE_CREATE = 'beforeCreate';

    const EVENT_AFTER_CREATE = 'afterCreate';

    const EVENT_BEFORE_UPDATE = 'beforeUpdate';

    const EVENT_AFTER_UPDATE = 'afterUpdate';

    const EVENT_BEFORE_DELETE = 'beforeDelete';

    const EVENT_AFTER_DELETE = 'afterDelete';

    const EVENT_BEFORE_SET_ACTIVE = 'beforeSetActive';

    const EVENT_AFTER_SET_ACTIVE = 'afterSetActive';

    const EVENT_BEFORE_SET_INACTIVE = 'beforeSetInactive';

    const EVENT_AFTER_SET_INACTIVE = 'afterSetInactive';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
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
                            'roles' => ['automation/au-folder/view', 'superadmin'],
                            'actions' => ['index', 'view']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['automation/au-folder/action', 'superadmin'],
                            'actions' => ['create', 'update', 'set-in-active', 'set-active', 'delete']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all AFolder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuFolderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AFolder model.
     * @param int $id آیدی
     * @param int $slave_id Slave ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     * @throws BadRequestHttpException
     * @throws \yii\base\ExitException
     */
    public function actionCreate($type = null)
    {
        $model = new AuFolder(['scenario' => AuFolder::SCENARIO_CREATE, 'type' => $type]);
        if (!$model->canCreate()) {
            throw new BadRequestHttpException($model->error_msg ?: Module::t('module', 'It is not possible to perform this operation'));
        }

        $result = [
            'success' => false,
            'msg' => Module::t('module', 'Error In Save Info')
        ];
        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $this->trigger(self::EVENT_BEFORE_CREATE, AuFolderEvent::create($model));
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    if ($flag) {
                        $this->trigger(self::EVENT_AFTER_CREATE, AuFolderEvent::create($model));
                        $result = [
                            'success' => true,
                            'msg' => Module::t('module', "Item Created") // درخواست با موفقیت انجام شد
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
            }
        } else {
            $model->loadDefaultValues();
        }
        $this->performAjaxValidation($model);
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->canUpdate()) {
            throw new BadRequestHttpException($model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
        }

        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->trigger(self::EVENT_BEFORE_UPDATE, AuFolderEvent::create($model));
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $model->save(false);
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_UPDATE, AuFolderEvent::create($model));
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
                Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
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
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->canDelete()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $this->trigger(self::EVENT_BEFORE_DELETE, AuFolderEvent::create($model));
                $flag = $model->softDelete();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_DELETE, AuFolderEvent::create($model));
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
            $this->trigger(self::EVENT_BEFORE_SET_ACTIVE, AuFolderEvent::create($model));
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = AuFolder::STATUS_ACTIVE;
                $flag = $model->save(false);
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_SET_ACTIVE, AuFolderEvent::create($model));
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
            $this->trigger(self::EVENT_BEFORE_SET_INACTIVE, AuFolderEvent::create($model));
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->status = AuFolder::STATUS_INACTIVE;
                $flag = $model->save(false);
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_SET_INACTIVE, AuFolderEvent::create($this));
                    $transaction->commit();
                    $result = [
                        'status' => true,
                        'message' => Module::t('module', 'Item Updated')
                    ];
                } else {

                    $transaction->rollBack();
                    $result = [
                        'status' => false,
                        'message' => Module::t('module', 'Error In Save Info')
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
                'message' => Module::t('module', 'It is not possible to perform this operation')
            ];
        }

        return $this->asJson($result);
    }

    /**
     * Finds the AFolder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id آیدی
     * @return AuFolder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuFolder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
