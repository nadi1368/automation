<?php

namespace hesabro\automation\controllers;

use hesabro\automation\events\AuLetterInternalEvent;
use hesabro\automation\models\AuLetterUser;
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
                            'roles' => ['automation/au-letter-internal/manage', 'superadmin'],
                            'actions' => ['index', 'view', 'print', 'create', 'confirm-and-send', 'confirm-and-start-work-flow', 'confirm-and-receive', 'update', 'delete']
                        ],
                        [
                            'allow' => true,
                            'roles' => ['automation/au-letter-internal/manage', 'superadmin'],
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
        $model = new AuLetter(['type' => AuLetter::TYPE_INTERNAL, 'date' => Yii::$app->jdf::jdate("Y/m/d")]);
        $model->setScenario(AuLetter::SCENARIO_CREATE_INTERNAL);
        if ($this->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                $this->trigger(self::EVENT_BEFORE_CREATE, AuLetterInternalEvent::create($model));
                try {
                    $flag = $model->save(false);
                    $flag = $flag && $model->createRecipientsInternal();
                    $flag = $flag && $model->createCCRecipientsInternal();
                    if ($flag) {
                        $this->trigger(self::EVENT_AFTER_CREATE, AuLetterInternalEvent::create($model));
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
     * Updates an existing AuLetter model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id آیدی
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
                $this->trigger(self::EVENT_BEFORE_UPDATE, AuLetterInternalEvent::create($model));
                try {
                    $flag = $model->save(false);
                    $flag = $flag && $model->updateRecipientsInternal($old_recipients);
                    $flag = $flag && $model->updateCCRecipientsInternal($old_cc_recipients);
                    if ($flag) {
                        $this->trigger(self::EVENT_AFTER_UPDATE, AuLetterInternalEvent::create($model));
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
