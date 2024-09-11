<?php

namespace hesabro\automation\controllers;

use hesabro\automation\Module;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use hesabro\automation\models\AuUser;
use hesabro\automation\models\AuUserSearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AuUserController implements the CRUD actions for AuUser model.
 */
class AuUserController extends Controller
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
                            'roles' => ['AuUser/index', 'superadmin'],
                            'actions' => ['index']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['AuUser/create', 'superadmin'],
                            'actions' => ['create']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['AuUser/update', 'superadmin'],
                            'actions' => ['update']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['AuUser/delete', 'superadmin'],
                            'actions' => ['delete']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['AuUser/view', 'superadmin'],
                            'actions' => ['view']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['AuLetter/create', 'superadmin'],
                            'actions' => ['get-user-list']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all AuUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single AuUser model.
     * @param int $id آیدی
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
     * @return string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionCreate()
    {
        $model = new AuUser();
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
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
     * @return string|\yii\web\Response
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
     * @return \yii\web\Response
     * @throws NotFoundHttpException
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
     * @param $q
     * @param $id
     * @return \yii\web\Response
     */
    public function actionGetUserList($q = null, $id = null)
    {
        $out = [];
        if (!is_null($q)) {
            $query = AuUser::find();

            $search_keys = explode(' ', $q);
            if (count($search_keys) > 1) {
                $like_condition[0] = 'AND';
                foreach ($search_keys as $key) {
                    $like_condition[] = ['like', "firstname", $key];
                }
            } else {
                $like_condition[0] = 'OR';
                $like_condition[] = ['like', "mobile", $q];
                $like_condition[] = ['like', "phone", $q];
                $like_condition[] = ['like', "firstname", $q];
            }

            $query->andWhere($like_condition);


            $data = $query->all();
            foreach ($data as $k => $user) {
                $out['results'][$k]['id'] = $user->id;
                $out['results'][$k]['text_show'] = $user->fullNameWithNumber;
                $out['results'][$k]['text'] = $user->firstname . ' ' . $user->lastname;
            }

        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => AuUser::findOne($id)->id];
        }
        return $this->asJson($out);
    }

    /**
     * Finds the AuUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id آیدی
     * @return AuUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuUser::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }
}
