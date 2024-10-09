<?php

namespace hesabro\automation\controllers;

use hesabro\automation\events\AuLetterInputEvent;
use hesabro\automation\models\AuLetterUser;
use hesabro\automation\Module;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use hesabro\automation\models\AuLetter;
use hesabro\automation\models\AuLetterSearch;
use yii\bootstrap4\Html;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Class AuLetterInputController
 * @package hesabro\automation\controllers
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AuLetterInputController extends AuLetterController
{
    use AjaxValidationTrait;

    public const EVENT_BEFORE_CONFIRM_AND_RECEIVE = 'beforeConfirmAndReceive';

    public const EVENT_AFTER_CONFIRM_AND_RECEIVE = 'afterConfirmAndReceive';

    public const EVENT_BEFORE_RUN_OCR = 'beforeRunOcr';

    public const EVENT_AFTER_RUN_OCR = 'afterRunOcr';

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
                            'roles' => ['automation/au-letter-input/manage', 'superadmin'],
                            'actions' => ['index', 'view', 'print', 'create', 'confirm-and-send', 'confirm-and-start-work-flow', 'confirm-and-receive', 'update', 'delete']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['automation/au-letter-input/manage', 'superadmin'],
                            'actions' => ['index', 'view', 'confirm-step', 'reference', 'answer', 'attach', 'signature']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['automation/au-letter/action'],
                            'actions' => ['view', 'confirm-step', 'reference', 'answer', 'attach', 'signature'],
                            'matchCallback' => function ($rule, $action) {
                                $letterId = Yii::$app->request->get('id', 0);
                                return AuLetterUser::find()->byUser(Yii::$app->user->id)->byLetter($letterId)->exists();
                            },
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
        $searchModel->type = AuLetter::TYPE_INPUT;
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
        $model = new AuLetter(['type' => AuLetter::TYPE_INPUT, 'date' => Yii::$app->jdf::jdate("Y/m/d")]);
        $model->setScenario(AuLetter::SCENARIO_CREATE_INPUT);
        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $this->trigger(self::EVENT_BEFORE_CREATE, AuLetterInputEvent::create($model));
                    $flag = $model->save(false);
                    $flag = $flag && $model->createRecipientsInternal();
                    $flag = $flag && $model->createCCRecipientsInternal();
                    if ($flag) {
                        $this->trigger(self::EVENT_AFTER_CREATE, AuLetterInputEvent::create($model));
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

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(AuLetter::SCENARIO_CREATE_INPUT);
        if (!$model->canUpdate()) {
            $this->flash('danger', Module::t('module', "Can Not Update"));
            return $this->redirect(['index']);
        }
        $model->recipients = $old_recipients = ArrayHelper::map($model->recipientUser, 'user_id', 'user_id');
        $model->cc_recipients = $old_cc_recipients = ArrayHelper::map($model->cCRecipientUser, 'user_id', 'user_id');

        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $this->trigger(self::EVENT_BEFORE_UPDATE, AuLetterInputEvent::create($model));
                    $flag = $model->save(false);
                    $flag = $flag && $model->updateRecipientsInternal($old_recipients);
                    $flag = $flag && $model->updateCCRecipientsInternal($old_cc_recipients);
                    if ($flag) {
                        $this->trigger(self::EVENT_AFTER_UPDATE, AuLetterInputEvent::create($model));
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
     * @param $id
     * @return string|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionConfirmAndReceive($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(AuLetter::SCENARIO_CONFIRM_AND_RECEIVE_INPUT);
        if (!$model->canConfirmAndReceive()) {
            throw new BadRequestHttpException($model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
        }

        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $this->trigger(self::EVENT_BEFORE_CONFIRM_AND_RECEIVE, AuLetterInputEvent::create($model));
                $flag = $model->confirmAndSend();  // save model
                $flag = $flag && $model->createRecipientsInternal();
                $flag = $flag && $model->createCCRecipientsInternal();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_CONFIRM_AND_RECEIVE, AuLetterInputEvent::create($model));
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
        return $this->renderAjax('_form-confirm-and-receive', [
            'model' => $model,
        ]);
    }

    public function actionRunOcr($id)
    {
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        $model = $this->findModel($id);
        if ($model->canRunOCR()) {
            if ($model->load(Yii::$app->request->post())) {
                $this->trigger(self::EVENT_BEFORE_RUN_OCR, AuLetterInputEvent::create($model));
                if ($model->save()) {
                    $this->trigger(self::EVENT_AFTER_RUN_OCR, AuLetterInputEvent::create($model));
                    $result['success'] = true;
                    $result['msg'] = 'عملیات با موفقیت ثبت شد.';
                } else {
                    $result['msg'] = Html::errorSummary($model);
                }
            } else {
                $ocr_text = $model->getFileTextByOCR();
                $model->body .= "\n" . $ocr_text;
                return $this->renderAjax('run-ocr', ['model' => $model]);
            }
        } else {
            $result['msg'] = Html::errorSummary($model);
        }

        return $this->asJson($result);
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
