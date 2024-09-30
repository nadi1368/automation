<?php

namespace hesabro\automation\controllers;

use hesabro\automation\events\AuLetterEvent;
use hesabro\automation\models\AuLetterActivity;
use hesabro\automation\models\AuPrintLayout;
use hesabro\automation\models\AuSignature;
use hesabro\automation\models\FormLetterAnswer;
use hesabro\automation\models\FormLetterReference;
use hesabro\automation\Module;
use hesabro\helpers\traits\AjaxValidationTrait;
use Yii;
use hesabro\automation\models\AuLetter;
use hesabro\automation\models\AuLetterSearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * AuLetterController implements the CRUD actions for AuLetter model.
 */
class AuLetterController extends Controller
{
    use AjaxValidationTrait;

    public const EVENT_BEFORE_CONFIRM_AND_SEND = 'beforeConfirmAndSend';

    public const EVENT_AFTER_CONFIRM_AND_SEND = 'afterConfirmAndSend';

    public const EVENT_BEFORE_REFERENCE = 'beforeReference';

    public const EVENT_AFTER_REFERENCE = 'afterReference';

    public const EVENT_BEFORE_ANSWER = 'beforeAnswer';

    public const EVENT_AFTER_ANSWER = 'afterAnswer';

    public const EVENT_BEFORE_ATTACH = 'beforeAttach';

    public const EVENT_AFTER_ATTACH = 'afterAttach';

    public const EVENT_BEFORE_SIGNATURE = 'beforeSignature';

    public const EVENT_AFTER_SIGNATURE = 'afterSignature';

    public const EVENT_BEFORE_CREATE = 'beforeCreate';

    public const EVENT_AFTER_CREATE = 'afterCreate';

    public const EVENT_BEFORE_UPDATE = 'beforeUpdate';

    public const EVENT_AFTER_UPDATE = 'afterUpdate';

    public const EVENT_BEFORE_DELETE = 'beforeDelete';

    public const EVENT_AFTER_DELETE = 'afterDelete';

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
                            'roles' => ['automation/au-letter/view', 'superadmin'],
                            'actions' => ['index', 'view', 'print']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['automation/au-letter/action', 'superadmin'],
                            'actions' => ['create', 'confirm-and-send', 'reference', 'answer', 'attach', 'signature', 'confirm-and-receive', 'update', 'delete']
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
        $dataProvider = $searchModel->searchMyFolder(Yii::$app->request->queryParams);

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
    public function actionPrint($id, $print_id = null)
    {
        $this->layout = 'print';
        $model = $this->findModel($id);
        $printLayout = $print_id ? $this->findModelPrint($print_id) : (new AuPrintLayout());
        return $this->render('/au-letter/print', [
            'model' => $model,
            'printLayout' => $printLayout,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /** @var $model AuLetter */
        $model = $this->findModel($id);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($model->canDelete()) {
                $this->trigger(self::EVENT_BEFORE_DELETE, AuLetterEvent::create($model));
                $flag = $model->softDelete();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_DELETE, AuLetterEvent::create($model));
                    $transaction->commit();
                    $this->flash('success', Module::t('module', "Item Deleted"));
                } else {
                    $transaction->rollBack();
                    $this->flash('warning', Module::t('module', "Error In Save Info"));
                }
            } else {
                $this->flash('warning', $model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->flash('warning', Module::t('module', "Error In Save Info"));
            Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
        }

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionConfirmAndSend($id)
    {
        /** @var $model AuLetter */
        $model = $this->findModel($id);
        $model->setScenario($model->type == AuLetter::TYPE_OUTPUT ? AuLetter::SCENARIO_CONFIRM_AND_SEND_OUTPUT : AuLetter::SCENARIO_CONFIRM_AND_SEND_INTERNAL);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($model->canConfirmAndSend()) {
                $this->trigger(self::EVENT_BEFORE_CONFIRM_AND_SEND, AuLetterEvent::create($model));
                $flag = $model->confirmAndSend();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_CONFIRM_AND_SEND, AuLetterEvent::create($model));
                    $transaction->commit();
                    $this->flash('success', Module::t('module', "Item Updated"));
                } else {
                    $transaction->rollBack();
                    $this->flash('warning', $model->error_msg ?: Module::t('module', "Error In Save Info"));
                }
            } else {
                $this->flash('warning', $model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->flash('warning', Module::t('module', "Error In Save Info"));
            Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionReference($id)
    {
        /** @var $model AuLetter */
        $model = $this->findModel($id);
        if (!$model->canReference()) {
            throw new BadRequestHttpException($model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
        }
        $modelReference = new FormLetterReference(['letter' => $model]);
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($modelReference->load(Yii::$app->request->post()) && $modelReference->validate()) {
            $this->trigger(self::EVENT_BEFORE_REFERENCE, AuLetterEvent::create($model));
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $modelReference->save();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_REFERENCE, AuLetterEvent::create($model));
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
        $this->performAjaxValidation($modelReference);
        return $this->renderAjax('/au-letter/_form-reference', [
            'modelReference' => $modelReference,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionAnswer($id)
    {
        /** @var $model AuLetter */
        $model = $this->findModel($id);
        if (!$model->canAnswer()) {
            throw new BadRequestHttpException($model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
        }
        $modelAnswer = new FormLetterAnswer(['letter' => $model]);
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($modelAnswer->load(Yii::$app->request->post()) && $modelAnswer->validate()) {
            $this->trigger(self::EVENT_BEFORE_ANSWER, AuLetterEvent::create($model));
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = $modelAnswer->save();
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_ANSWER, AuLetterEvent::create($model));
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
        $this->performAjaxValidation($modelAnswer);
        return $this->renderAjax('/au-letter/_form-answer', [
            'modelAnswer' => $modelAnswer,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionAttach($id)
    {
        /** @var $model AuLetter */
        $model = $this->findModel($id);
        if (!$model->canAttach()) {
            throw new BadRequestHttpException($model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
        }
        $modelAttach = new AuLetterActivity(['scenario' => AuLetterActivity::SCENARIO_ATTACH, 'type' => AuLetterActivity::TYPE_ATTACH, 'letter_id' => $model->id]);
        $result = [
            'success' => false,
            'msg' => Module::t('module', "Error In Save Info")
        ];
        if ($modelAttach->load(Yii::$app->request->post()) && $modelAttach->validate()) {
            $this->trigger(self::EVENT_BEFORE_ATTACH, AuLetterEvent::create($model));
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = true;
                $modelAttach->file = UploadedFile::getInstances($modelAttach, 'file');

                foreach (is_array($modelAttach->file) ? $modelAttach->file : [$modelAttach->file] as $file) {
                    $modelAttachMultiple = new AuLetterActivity(['scenario' => AuLetterActivity::SCENARIO_ATTACH, 'type' => AuLetterActivity::TYPE_ATTACH, 'letter_id' => $model->id]);
                    $modelAttachMultiple->file = $file;
                    $flag = $flag && $modelAttachMultiple->save(false);
                    if ($model->type == AuLetter::TYPE_OUTPUT && $model->input_type == AuLetter::INPUT_OUTPUT_SYSTEM) {
                        $model->attaches[$modelAttachMultiple->id] = $modelAttachMultiple->id;
                    }
                }
                if ($model->type == AuLetter::TYPE_OUTPUT && $model->input_type == AuLetter::INPUT_OUTPUT_SYSTEM) {
                    $flag = $flag && $model->save(false);
                }
                if ($flag) {
                    $this->trigger(self::EVENT_AFTER_ATTACH, AuLetterEvent::create($model));
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
        $this->performAjaxValidation($modelAttach);
        return $this->renderAjax('/au-letter/_form-attach', [
            'modelAttach' => $modelAttach,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSignature($id, $signature_id)
    {
        /** @var $model AuLetter */
        $model = $this->findModel($id);
        $signature = $this->findModelSignature($signature_id);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if ($signature->canUse()) {
                if ($model->canSignature()) {
                    $this->trigger(self::EVENT_BEFORE_SIGNATURE, AuLetterEvent::create($model));
                    $flag = $model->signature($signature);
                    if ($flag) {
                        $this->trigger(self::EVENT_AFTER_SIGNATURE, AuLetterEvent::create($model));
                        $transaction->commit();
                        $this->flash('success', Module::t('module', "Item Updated"));
                    } else {
                        $transaction->rollBack();
                        $this->flash('warning', $model->error_msg ?: Module::t('module', "Error In Save Info"));
                    }
                } else {
                    $this->flash('warning', $model->error_msg ?: Module::t('module', "It is not possible to perform this operation"));
                }
            } else {
                $this->flash('warning', 'شما دسترسی استفاده از این امضا را ندارید');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->flash('warning', Module::t('module', "Error In Save Info"));
            Yii::error($e->getMessage() . $e->getTraceAsString(), Yii::$app->controller->id . '/' . Yii::$app->controller->action->id);
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }


    /**
     * Finds the AuSignature model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id آیدی
     * @return AuSignature the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelSignature($id)
    {
        if (($model = AuSignature::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Module::t('module', 'The requested page does not exist.'));
    }

    /**
     * Finds the AuPrintLayout model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id آیدی
     * @return AuPrintLayout the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelPrint($id)
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
