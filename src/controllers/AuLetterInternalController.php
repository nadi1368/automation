<?php

namespace hesabro\automation\controllers;

use hesabro\automation\Module;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use hesabro\automation\models\AuLetter;
use hesabro\automation\models\AuLetterSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Class AuLetterInternalController
 * @package hesabro\automation\controllers
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AuLetterInternalController extends AuLetterController
{
    use AjaxValidationTrait;

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
                            'roles' => ['AuLetter/index', 'superadmin'],
                            'actions' => ['index']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['AuLetter/create', 'superadmin'],
                            'actions' => ['create', 'confirm-and-send', 'reference', 'answer', 'attach', 'signature', 'confirm-and-receive']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['AuLetter/update', 'superadmin'],
                            'actions' => ['update']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['AuLetter/delete', 'superadmin'],
                            'actions' => ['delete']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['AuLetter/view', 'superadmin'],
                            'actions' => ['view', 'print']
                        ],
                    ]
            ]
        ];
    }

    /**
     * Lists all AuLetter models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuLetterSearch();
        $searchModel->type = AuLetter::TYPE_INTERNAL;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single AuLetter model.
     * @param int $id آیدی
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->afterView();
        return $this->render('view', [
            'model' => $model,
        ]);
    }


    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new AuLetter(['type' => AuLetter::TYPE_INTERNAL, 'date' => Yii::$app->jdate->date("Y/m/d")]);
        $model->setScenario(AuLetter::SCENARIO_CREATE_INTERNAL);
        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    $flag = $flag && $model->createRecipientsInternal();
                    $flag = $flag && $model->createCCRecipientsInternal();
                    if ($flag) {
                        $transaction->commit();
                        $this->flash('success', Module::t('module', "Item Created"));
                        return $this->redirect(['view', 'id' => $model->id, 'slave_id' => $model->slave_id]);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing AuLetter model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id آیدی
     * @param int $slave_id Slave ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(AuLetter::SCENARIO_CREATE_INTERNAL);
        if (!$model->canUpdate()) {
            $this->flash('danger', Module::t('module', 'Can Not Update'));
            return $this->redirect(['index']);
        }
        $model->recipients = $old_recipients = ArrayHelper::map($model->recipientUser, 'user_id', 'user_id');
        $model->cc_recipients = $old_cc_recipients = ArrayHelper::map($model->cCRecipientUser, 'user_id', 'user_id');

        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $flag = $model->save(false);
                    $flag = $flag && $model->updateRecipientsInternal($old_recipients);
                    $flag = $flag && $model->updateCCRecipientsInternal($old_cc_recipients);
                    if ($flag) {
                        $transaction->commit();
                        $this->flash('success', Module::t('module', "Item Created"));
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
                }
            }
        } else {
            $model->loadDefaultValues();
        }


        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the AuLetter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id آیدی
     * @return AuLetter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuLetter::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

}
