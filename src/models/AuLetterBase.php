<?php

namespace hesabro\automation\models;

use hesabro\automation\Module;
use hesabro\notif\behaviors\NotifBehavior;
use hesabro\notif\interfaces\NotifInterface;
use Yii;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use hesabro\helpers\behaviors\JsonAdditional;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "automation_letter".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int|null $sender_id فرستنده
 * @property int $type
 * @property string|null $title
 * @property int|null $folder_id
 * @property string|null $body
 * @property int|null $number
 * @property string|null $input_number شماره نامه وارده
 * @property int $input_type نحوه دریافت نامه وارده
 * @property string|null $date
 * @property string|null $additional_data
 * @property int $status
 * @property int $current_step
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $deleted_at
 * @property int $slave_id
 *
 * @property AuLetterUser[] $user
 * @property AuLetterUser[] $recipientUser
 * @property AuLetterUser[] $workFlowUser
 * @property AuLetterClient[] $recipientClient
 * @property AuLetterClientWithoutSlave[] $recipientClientWithoutSlave
 * @property AuLetterUser[] $cCRecipientUser
 * @property AuLetterUser[] $reference
 * @property AuLetterActivity[] $activity
 * @property AuWorkFlow $workFlow
 * @property AuFolder $folder
 * @property object $creator
 * @property object $update
 * @property string $printNumber
 * @property string $printCountAttach
 * @property bool $viewed
 * @property bool $isWorkFlow
 * @property int|null $workflow_id
 * @property int|null $workflow_id_value
 */
class AuLetterBase extends ActiveRecord implements NotifInterface
{
    const STATUS_DELETED = 0;
    const STATUS_DRAFT = 1; // پیش نویش
    const STATUS_CONFIRM_AND_SEND = 2; // تایید و ارسال
    const STATUS_WAIT_CONFIRM = 3; // در انتظار تایید نامه های وارده بین سیستمی

    const TYPE_INTERNAL = 1; // داخلی
    const TYPE_INPUT = 2; // وارده
    const TYPE_OUTPUT = 3; // صادره
    const TYPE_RECORD = 4; // صورت جلسه مالی


    const INPUT_OUTPUT_SYSTEM = 1; // نامه وارده سیستمی
    const INPUT_OUTPUT_CUSTOMER = 2;
    const INPUT_OUTPUT_DELIVERY = 3;
    const INPUT_OUTPUT_EMAIL = 4;
    const INPUT_OUTPUT_FAX = 5;
    const INPUT_OUTPUT_OTHER = 6;

    const SCENARIO_CREATE_INTERNAL = 'create_internal';
    const SCENARIO_CREATE_INPUT = 'create_input';
    const SCENARIO_CONFIRM_AND_RECEIVE_INPUT = 'confirm_and_receive_input';
    const SCENARIO_CONFIRM_AND_SEND_INTERNAL = 'confirm_and_send_internal';
    const SCENARIO_CONFIRM_AND_SEND_OUTPUT = 'confirm_and_send_output';
    const SCENARIO_CONFIRM_AND_SEND_RECORD = 'confirm_and_send_record';
    const SCENARIO_RECEIVE_INPUT = 'RECEIVE_INPUT';
    const SCENARIO_CREATE_OUTPUT = 'create_output';
    const SCENARIO_CONFIRM_AND_START_WORK_FLOW = 'confirm_and_start_work_flow';
    const SCENARIO_CREATE_RECORD = 'create_record';
    const SCENARIO_CONFIRM_AND_NEXT_STEP = 'confirm_and_next_step';

    const NOTIF_AU_LETTER_CONFIRM_AND_START_WORK_FLOW = 'notif_au_letter_confirm_and_start_work_flow';

    const NOTIF_AU_LETTER_CONFIRM_AND_NEXT_STEP = 'notif_au_letter_confirm_and_next_step';

    const NOTIF_AU_LETTER_CONFIRM_AND_END_STEPS = 'notif_au_letter_confirm_and_end_steps';

    const NOTIF_AU_LETTER_RECEIVE_INPUT = 'notif_au_letter_receive_input';

    public $error_msg = '';
    public $recipients; // گیرندگان
    public $cc_recipients; // رونوشت

    // ----- ADDITIONAL DATA ------
    public $attaches; // فایل های پیوست
    public $signatures;
    public $files_text;
    public $header_text = '';
    public $footer_text = '';
    public $total_step = 0;

    public mixed $workflow_id_value = null;
    // ----------------------------

    private $_viewd = null;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%automation_letter}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'sender_id', 'type', 'folder_id', 'number', 'input_type', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id'], 'integer'],
            [['type', 'title', 'folder_id', 'date', 'recipients'], 'required', 'on' => self::SCENARIO_CREATE_INTERNAL],
            [['type', 'title', 'date', 'body'], 'required', 'on' => self::SCENARIO_CREATE_RECORD],
            [['type', 'title', 'folder_id', 'date', 'recipients', 'sender_id', 'input_type'], 'required', 'on' => self::SCENARIO_CREATE_INPUT],
            [['type', 'title', 'folder_id', 'date', 'sender_id', 'recipients'], 'required', 'on' => self::SCENARIO_CREATE_OUTPUT],
            [['folder_id', 'recipients'], 'required', 'on' => self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT],
            [['folder_id'], 'required', 'on' => [self::SCENARIO_CONFIRM_AND_SEND_INTERNAL, self::SCENARIO_CONFIRM_AND_SEND_OUTPUT, self::SCENARIO_CONFIRM_AND_SEND_RECORD]],
            [['recipients', 'cc_recipients'], 'safe', 'on' => [self::SCENARIO_CREATE_INTERNAL, self::SCENARIO_CREATE_OUTPUT, self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT]],
            [['body'], 'string'],
            [['title'], 'string', 'max' => 64],
            [['input_number'], 'string', 'max' => 32],
            [['date'], 'string', 'max' => 10],
            [['input_number'], 'default', 'value' => ''],
            [['input_type'], 'default', 'value' => 1],
            [['folder_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuFolder::class, 'targetAttribute' => ['folder_id' => 'id']],
            ['workflow_id_value', 'exist', 'targetClass' => AuWorkFlow::class, 'targetAttribute' => ['workflow_id_value' => 'id']]
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE_INTERNAL] = ['title', 'folder_id', 'date', 'body', 'recipients', 'cc_recipients', 'workflow_id_value'];
        $scenarios[self::SCENARIO_CREATE_RECORD] = ['title', 'date', 'body'];
        $scenarios[self::SCENARIO_CREATE_INPUT] = ['title', 'folder_id', 'date', 'body', 'recipients', 'cc_recipients', 'sender_id', 'input_type', 'input_number', 'workflow_id_value'];
        $scenarios[self::SCENARIO_CREATE_OUTPUT] = ['title', 'folder_id', 'date', 'body', 'sender_id', 'recipients', 'cc_recipients', 'input_type', 'workflow_id_value'];
        $scenarios[self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT] = ['folder_id', 'recipients', 'cc_recipients'];
        $scenarios[self::SCENARIO_CONFIRM_AND_SEND_INTERNAL] = ['folder_id', '!status'];
        $scenarios[self::SCENARIO_CONFIRM_AND_SEND_OUTPUT] = ['folder_id', '!status'];
        $scenarios[self::SCENARIO_CONFIRM_AND_SEND_RECORD] = ['folder_id', '!status'];
        $scenarios[self::SCENARIO_RECEIVE_INPUT] = ['!type'];
        $scenarios[self::SCENARIO_CONFIRM_AND_START_WORK_FLOW] = ['!status'];
        $scenarios[self::SCENARIO_CONFIRM_AND_NEXT_STEP] = ['!status'];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'parent_id' => Module::t('module', 'Parent ID'),
            'sender_id' => 'فرستنده',
            'recipients' => 'گیرندگان',
            'cc_recipients' => 'رونوشت',
            'type' => Module::t('module', 'Type'),
            'title' => 'موضوع',
            'folder_id' => 'اندیکاتور',
            'workflow_id' => 'گردش کار',
            'workflow_id_value' => 'گردش کار',
            'body' => Module::t('module', 'Description'),
            'number' => Module::t('module', 'Number'),
            'input_number' => 'شماره نامه وارده',
            'input_type' => $this->type == self::TYPE_OUTPUT ? 'نحوه ارسال' : 'نحوه دریافت',
            'date' => Module::t('module', 'Date'),
            'additional_data' => Module::t('module', 'Additional Data'),
            'status' => Module::t('module', 'Status'),
            'created_at' => Module::t('module', 'Created At'),
            'created_by' => Module::t('module', 'Created By'),
            'updated_at' => Module::t('module', 'Updated At'),
            'updated_by' => Module::t('module', 'Updated By'),
            'deleted_at' => Module::t('module', 'Deleted At'),
            'slave_id' => Module::t('module', 'Slave ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasMany(AuLetterUser::class, ['letter_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipientUser()
    {
        return $this->hasMany(AuLetterUser::class, ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_RECIPIENTS]);
    }

    /**
     * @return mixed
     */
    public function getWorkFlowUser()
    {
        return $this->hasMany(AuLetterUser::class, ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_WORK_FLOW]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipientClient()
    {
        return $this->hasMany(AuLetterClient::class, ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_RECIPIENTS]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipientClientWithoutSlave()
    {
        return $this->hasMany(AuLetterClientWithoutSlave::class, ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_RECIPIENTS]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCCRecipientUser()
    {
        return $this->hasMany(AuLetterUser::class, ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_CC_RECIPIENTS]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReference()
    {
        return $this->hasMany(AuLetterUser::class, ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_REFERENCE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasMany(AuLetterActivity::class, ['letter_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkFlow()
    {
        return $this->hasOne(AuWorkFlow::class, ['id' => 'workflow_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFolder()
    {
        return $this->hasOne(AuFolder::class, ['id' => 'folder_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdate()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'updated_by']);
    }

    public function getWorkflow_id()
    {
        if ($this->type === self::TYPE_RECORD) {
            return Module::getInstance()->settings::get('automation_record_workflow_id', null);
        }

        return $this->workflow_id_value;
    }

    public function getIsWorkFlow()
    {
        return !empty($this->workflow_id);
    }

    /**
     * {@inheritdoc}
     * @return AuLetterQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuLetterQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate()
    {

        if (in_array($this->type, [self::TYPE_INPUT, self::TYPE_OUTPUT]) && $this->input_type != self::INPUT_OUTPUT_SYSTEM && $this->created_at > strtotime("-3 DAY")) {
            // نامه های صادره و وارده که سیستمی نیستند تا 3 روز بعد قابل بروز رسانی می باشند
            return true;
        }
        if ($this->status == self::STATUS_WAIT_CONFIRM && $this->isWorkFlow && $this->current_step === 0) {
            return true;
        }

        return $this->status == self::STATUS_DRAFT;
    }

    public function canDelete()
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function canConfirmAndSend()
    {
        if ($this->status == self::STATUS_WAIT_CONFIRM && $this->isWorkFlow && $this->total_step > 0) {
            //اگر نامه دارای فرایند گردش کار بود بعد از تایید اخرین مرجله میتواند شماره بخورد
            return $this->current_step > $this->total_step;
        }
        return $this->status == self::STATUS_DRAFT;
    }

    /**
     * @return bool
     * تایید نامه های دارای گردش کار
     * و شروع فرایند گردش کار
     */
    public function canConfirmAndStartWorkFlow()
    {
        return $this->status == self::STATUS_WAIT_CONFIRM && $this->isWorkFlow && $this->current_step === 0;
    }

    /**
     * @return false
     * تایید نامه در این مرحله توسط شخص مربوطه
     */
    public function canConfirmInCurrentStep()
    {
        if ($this->status == self::STATUS_WAIT_CONFIRM && $this->isWorkFlow && $this->current_step > 0) {
            return $this->getWorkFlowUser()
                ->byStep($this->current_step)
                ->byUser(Yii::$app->user->id)
                ->byStatus([AuLetterUser::STATUS_WAIT_VIEW, AuLetterUser::STATUS_VIEWED])
                ->exists();
        }
        return false;
    }

    /**
     * @return false
     * رد نامه در این مرحله توسط شخص مربوطه
     */
    public function canRejectInCurrentStep()
    {
        if ($this->status == self::STATUS_WAIT_CONFIRM && $this->isWorkFlow && $this->current_step > 0) {
            return $this->getWorkFlowUser()
                ->byStep($this->current_step)
                ->byUser(Yii::$app->user->id)
                ->byStatus([AuLetterUser::STATUS_WAIT_VIEW, AuLetterUser::STATUS_VIEWED])
                ->exists();
        }
        return false;
    }

    public function canPrintWithSenderLayout()
    {
        return $this->type == self::TYPE_INPUT && $this->input_type == self::INPUT_OUTPUT_SYSTEM;
    }

    public function canPrint()
    {
        return $this->status == self::STATUS_CONFIRM_AND_SEND;
    }

    public function canConfirmAndReceive()
    {
        return $this->status == self::STATUS_WAIT_CONFIRM && $this->type == self::TYPE_INPUT && !$this->isWorkFlow;
    }

    public function canReference()
    {
        if ($this->type == self::TYPE_OUTPUT) {
            return $this->status == self::STATUS_DRAFT;
        }
        return $this->status == self::STATUS_DRAFT || $this->status == self::STATUS_CONFIRM_AND_SEND;
    }

    public function canAnswer()
    {
        if ($this->type == self::TYPE_OUTPUT) {
            return $this->status == self::STATUS_DRAFT;
        }
        return $this->status == self::STATUS_DRAFT || $this->status == self::STATUS_CONFIRM_AND_SEND;
    }

    public function canAttach()
    {
        if ($this->type == self::TYPE_OUTPUT) {
            return $this->status == self::STATUS_DRAFT;
        }
        return $this->status == self::STATUS_DRAFT || $this->status == self::STATUS_CONFIRM_AND_SEND;
    }

    public function canSignature()
    {
        if ($this->type == self::TYPE_OUTPUT) {
            return $this->status == self::STATUS_DRAFT;
        }
        return $this->status == self::STATUS_DRAFT || $this->status == self::STATUS_CONFIRM_AND_SEND;
    }

    public function canRunOCR(): bool
    {
        return $this->status == self::TYPE_INPUT && $this->status == self::STATUS_CONFIRM_AND_SEND;
    }

    /**
     * @return int
     */
    public function countAttach(): int
    {
        return (int)$this->getActivity()->andWhere([AuLetterActivity::tableName() . '.type' => AuLetterActivity::TYPE_ATTACH])->count();
    }

    /**
     * @return int|string
     */
    public function getPrintCountAttach()
    {
        return ($countAttach = $this->countAttach()) > 0 ? $countAttach : '';
    }

    /**
     * @return bool
     * @throws yii\db\Exception
     */
    public function createRecipientsInternal(): bool
    {
        foreach (is_array($this->recipients) ? $this->recipients : [] as $userId) {
            $auLetterUser = new AuLetterUser(['type' => AuLetterUser::TYPE_RECIPIENTS]);
            $auLetterUser->title = 'گیرنده نامه';
            $auLetterUser->letter_id = $this->id;
            $auLetterUser->user_id = $userId;
            if (!$auLetterUser->save()) {
                $this->addError('recipients', $auLetterUser->getFirstError('user_id'));
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws yii\db\Exception
     */
    public function createRecipientsOutput(): bool
    {
        $recipients = [];
        foreach (is_array($this->recipients) ? $this->recipients : [] as $clientId) {
            if (str_contains($clientId, 'g_')) {
                [, $auClientGroupId] = explode('g_', $clientId);
                if (($auClientGroup = AuClientGroup::findOne((int)$auClientGroupId)) !== null) {
                    foreach (is_array($auClientGroup->clients) ? $auClientGroup->clients : [] as $clientIdFromGroup) {
                        if (!in_array($clientIdFromGroup, $recipients)) {
                            $recipients[] = (int)$clientIdFromGroup;
                        }
                    }
                }
            } elseif (!in_array($clientId, $recipients)) {
                $recipients[] = (int)$clientId;
            }
        }
        foreach ($recipients as $clientId) {
            $auLetterClient = new AuLetterClient(['type' => AuLetterUser::TYPE_RECIPIENTS]);
            $auLetterClient->title = 'گیرنده نامه';
            $auLetterClient->letter_id = $this->id;
            $auLetterClient->client_id = $clientId;
            if (!$auLetterClient->save()) {
                $this->addError('recipients', $auLetterClient->getFirstError('client_id'));
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws yii\db\Exception
     */
    public function createCCRecipientsInternal(): bool
    {
        foreach (is_array($this->cc_recipients) ? $this->cc_recipients : [] as $userId) {
            $auLetterUser = new AuLetterUser(['type' => AuLetterUser::TYPE_CC_RECIPIENTS]);
            $auLetterUser->title = 'رونوشت نامه';
            $auLetterUser->letter_id = $this->id;
            $auLetterUser->user_id = $userId;
            if (!$auLetterUser->save()) {
                $this->addError('cc_recipients', $auLetterUser->getFirstError('user_id'));
                return false;
            }
        }
        return true;
    }

    /**
     * @param $old_recipients
     * @return bool
     * @throws \Throwable
     * @throws yii\db\Exception
     * @throws yii\db\StaleObjectException
     */
    public function updateRecipientsInternal($old_recipients): bool
    {

        $this->recipients = is_array($this->recipients) ? $this->recipients : [];
        $deleted_recipients_ids = array_diff($old_recipients, $this->recipients);
        if (!empty($deleted_recipients_ids)) {
            foreach ($deleted_recipients_ids as $userId) {
                if (($model = AuLetterUser::find()->andWhere(['user_id' => $userId, 'letter_id' => $this->id, 'type' => AuLetterUser::TYPE_RECIPIENTS])->limit(1)->one()) !== null && !$model->softDelete()) {
                    return false;
                }
            }
        }
        foreach (array_diff($this->recipients, $old_recipients) as $userId) {
            $auLetterUser = new AuLetterUser(['type' => AuLetterUser::TYPE_RECIPIENTS]);
            $auLetterUser->title = 'گیرنده نامه';
            $auLetterUser->letter_id = $this->id;
            $auLetterUser->user_id = $userId;
            if (!$auLetterUser->save()) {
                $this->addError('recipients', $auLetterUser->getFirstError('user_id'));
                return false;
            }
        }
        return true;
    }

    /**
     * @param $old_recipients
     * @return bool
     * @throws \Throwable
     * @throws yii\db\Exception
     * @throws yii\db\StaleObjectException
     */
    public function updateRecipientsOutput($old_recipients): bool
    {

        $recipients = [];
        foreach (is_array($this->recipients) ? $this->recipients : [] as $clientId) {
            if (str_contains($clientId, 'g_')) {
                [, $auClientGroupId] = explode('g_', $clientId);
                if (($auClientGroup = AuClientGroup::findOne((int)$auClientGroupId)) !== null) {
                    foreach (is_array($auClientGroup->clients) ? $auClientGroup->clients : [] as $clientIdFromGroup) {
                        if (!in_array($clientIdFromGroup, $recipients)) {
                            $recipients[] = (int)$clientIdFromGroup;
                        }
                    }
                }
            } elseif (!in_array($clientId, $recipients)) {
                $recipients[] = (int)$clientId;
            }
        }


        $deleted_recipients_ids = array_diff($old_recipients, $recipients);
        if (!empty($deleted_recipients_ids)) {
            foreach ($deleted_recipients_ids as $userId) {
                if (($model = AuLetterClient::find()->andWhere(['client_id' => $userId, 'letter_id' => $this->id, 'type' => AuLetterClient::TYPE_RECIPIENTS])->limit(1)->one()) !== null && !$model->softDelete()) {
                    return false;
                }
            }
        }
        foreach (array_diff($recipients, $old_recipients) as $userId) {
            $auLetterUser = new AuLetterClient(['type' => AuLetterClient::TYPE_RECIPIENTS]);
            $auLetterUser->title = 'گیرنده نامه';
            $auLetterUser->letter_id = $this->id;
            $auLetterUser->client_id = $userId;
            if (!$auLetterUser->save()) {
                $this->addError('recipients', $auLetterUser->getFirstError('user_id'));
                return false;
            }
        }
        return true;
    }

    /**
     * @param $old_cc_recipients
     * @return bool
     * @throws \Throwable
     * @throws yii\db\Exception
     * @throws yii\db\StaleObjectException
     */
    public function updateCCRecipientsInternal($old_cc_recipients): bool
    {
        $this->cc_recipients = is_array($this->cc_recipients) ? $this->cc_recipients : [];
        $deleted_cc_recipients_ids = array_diff($old_cc_recipients, $this->cc_recipients);
        if (!empty($deleted_cc_recipients_ids)) {
            foreach ($deleted_cc_recipients_ids as $userId) {
                if (($model = AuLetterUser::find()->andWhere(['user_id' => $userId, 'letter_id' => $this->id, 'type' => AuLetterUser::TYPE_CC_RECIPIENTS])->limit(1)->one()) !== null && !$model->softDelete()) {
                    return false;
                }
            }
        }

        foreach (array_diff($this->cc_recipients, $old_cc_recipients) as $userId) {
            $auLetterUser = new AuLetterUser(['type' => AuLetterUser::TYPE_CC_RECIPIENTS]);
            $auLetterUser->title = 'رونوشت نامه';
            $auLetterUser->letter_id = $this->id;
            $auLetterUser->user_id = $userId;
            if (!$auLetterUser->save()) {
                $this->addError('cc_recipients', $auLetterUser->getFirstError('user_id'));
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws yii\db\Exception
     */
    public function confirmAndSend()
    {
        $this->status = self::STATUS_CONFIRM_AND_SEND;
        $lastNumber = $this->folder->getAndSaveLastNumber();
        if ($lastNumber > 0) {
            $this->number = $lastNumber;
            return $this->save(false);
        }
        $this->error_msg = 'خطا در شماره گذاری نامه.لطفا مجددا تلاش نمایید.';
        return false;
    }

    /**
     * @return bool
     * @throws yii\db\Exception
     */
    public function confirmAndStartWorkFlow()
    {
        $workFlow = $this->workFlow;
        /** @var AuWorkFlowStep $item */
        foreach (($workFlow?->stepsByOrder ?: []) as $item) {
            foreach (is_array($item->users) ? $item->users : [] as $userId) {
                $auLetterUser = new AuLetterUser(['type' => AuLetterUser::TYPE_WORK_FLOW]);
                $auLetterUser->title = $workFlow->title;
                $auLetterUser->letter_id = $this->id;
                $auLetterUser->user_id = $userId;
                $auLetterUser->step = $item->step;
                $auLetterUser->operation_type = $item->operation_type;
                if (!$auLetterUser->save()) {
                    $this->addError('recipients', $auLetterUser->getFirstError('user_id'));
                    return false;
                }
            }
        }

        $this->current_step = 1;
        return $this->save(false);
    }


    /**
     * @param AuSignature $signature
     * @return bool
     * @throws yii\db\Exception
     */
    public function signature(AuSignature $signature): bool
    {
        $auLetterActivity = new AuLetterActivity();
        $auLetterActivity->letter_id = $this->id;
        $auLetterActivity->type = AuLetterActivity::TYPE_SIGNATURE;
        $auLetterActivity->signatureId = $signature->id;
        $flag = $auLetterActivity->save();
        $this->signatures[$signature->id] = $signature->title;
        return $flag && $this->save(false);
    }

    /**
     * @return bool
     */
    public function getViewed()
    {
        if ($this->_viewd === null) {
            $this->_viewd = !$this->getUser()->andWhere(['user_id' => Yii::$app->user->id, 'status' => AuLetterUser::STATUS_WAIT_VIEW])->exists();
        }
        return $this->_viewd;
    }

    /**
     * @return void
     * @throws yii\db\Exception
     */
    public function afterView(): void
    {
        if ($this->type == self::TYPE_INPUT && $this->input_type == self::INPUT_OUTPUT_SYSTEM && Yii::$app->client && ($auLetterClientWithoutSlave = AuLetterClientWithoutSlave::find()->byClient(Yii::$app->client->id)->byLetter($this->parent_id)->byStatus(AuLetterClient::STATUS_WAIT_VIEW)->limit(1)->one()) !== null) {
            $auLetterClientWithoutSlave->status = AuLetterClient::STATUS_VIEWED;
            $auLetterClientWithoutSlave->save(false);
        }
        if ($this->status == self::STATUS_CONFIRM_AND_SEND && ($auUser = $this->getUser()->andWhere(['user_id' => Yii::$app->user->id, 'status' => AuLetterUser::STATUS_WAIT_VIEW])->limit(1)->one()) !== null) {
            $auUser->status = AuLetterUser::STATUS_VIEWED;
            $auUser->save(false);
        }
        if ($this->status == self::STATUS_WAIT_CONFIRM && $this->isWorkFlow && $this->current_step > 0 && ($auUser = $this->getWorkFlowUser()->byStep($this->current_step)->byUser(Yii::$app->user->id)->byStatus([AuLetterUser::STATUS_WAIT_VIEW])->limit(1)->one()) !== null) {
            $auUser->status = AuLetterUser::STATUS_VIEWED;
            $auUser->save(false);
        }
    }

    /**
     * @return bool
     * بعد از تایید یکی از اشخاص این مرحله
     */
    public function afterConfirmUserInCurrentStep()
    {
        $findWait = $this->getWorkFlowUser()->byStep($this->current_step)->byStatus([AuLetterUser::STATUS_WAIT_VIEW, AuLetterUser::STATUS_VIEWED])->limit(1)->one();
        /** @var AuLetterUser $findWait */
        if ($findWait === null || $findWait->operation_type == AuWorkFlowStep::OPERATION_TYPE_OR) {
            $this->setScenario(self::SCENARIO_CONFIRM_AND_NEXT_STEP);
            $this->current_step++;
            return $this->save(false);
        }
        return true;
    }

    /**
     * @return bool
     * بعد از تایید یکی از اشخاص این مرحله
     */
    public function afterRejectUserInCurrentStep()
    {
        /** @var AuWorkFlow $item */
        foreach (AuLetterUser::find()->byLetter($this->id)->byType(AuLetterUser::TYPE_WORK_FLOW)->all() as $auUser) {
            if (!$auUser->softDelete()) {
                return false;
            }
        }
        $this->current_step = 0;
        return $this->save(false);
    }

    /**
     * @return string
     */
    public function showSender()
    {
        $sender = '';
        if ($this->sender_id) {
            if ($this->type == self::TYPE_INPUT) {
                if ($this->input_type == self::INPUT_OUTPUT_SYSTEM) {
                    $clientClass = Module::getInstance()->client;
                    $client = $clientClass::findOne($this->sender_id);
                    $sender = $client?->title;
                } else {
                    $auUser = AuUser::findOne($this->sender_id);
                    $sender = $auUser?->fullName;
                }
            } else {
                $userClass = Module::getInstance()->user;
                $user = $userClass::findOne($this->sender_id);
                $sender = $user?->fullName;
            }
        }
        return $sender;
    }

    /**
     * @return string
     */
    public function showRecipientsList()
    {
        $list = '';
        foreach ($this->recipientUser as $auLetterUser) {
            $list .= Html::tag('label', Html::tag('i', '', ['class' => AuLetterUser::itemAlias('StatusIcon', $auLetterUser->status) . ' mr-1']) . $auLetterUser->user?->fullName, ['class' => 'badge badge-info mr-2 mb-2', 'title' => AuLetterUser::itemAlias('Status', $auLetterUser->status)]);
        }
        return $list;
    }

    /**
     * @return string
     */
    public function showWorkFlowUserList($step)
    {
        $list = '';
        foreach ($this->getWorkFlowUser()->byStep($step)->all() as $auLetterUser) {
            $list .= Html::tag('label', Html::tag('i', '', ['class' => AuLetterUser::itemAlias('StatusIconWorkFlow', $auLetterUser->status) . ' mr-1']) . $auLetterUser->user?->fullName, ['class' => 'badge badge-info mr-2 mb-2', 'title' => AuLetterUser::itemAlias('Status', $auLetterUser->status)]) . '<br />';
        }
        return $list;
    }

    /**
     * @return string
     */
    public function showRecipientsOutputList()
    {
        $list = '';
        if ($this->input_type == self::INPUT_OUTPUT_SYSTEM) {
            // نامه های بین سیستمی
            foreach ($this->recipientClient as $auLetterClient) {
                $list .= Html::tag('label', Html::tag('i', '', ['class' => AuLetterClient::itemAlias('StatusIcon', $auLetterClient->status) . ' mr-1']) . $auLetterClient->client?->title, ['class' => 'badge badge-info mr-2 mb-2', 'title' => AuLetterClient::itemAlias('Status', $auLetterClient->status)]);
            }
        } else {
            // نامه های خارج سیستمی
            foreach ($this->recipientUser as $auLetterUser) {
                $list .= Html::tag('label', Html::tag('i', '', ['class' => AuLetterUser::itemAlias('StatusIcon', $auLetterUser->status) . ' mr-1']) . $auLetterUser->auUser?->fullName, ['class' => 'badge badge-info mr-2 mb-2', 'title' => AuLetterUser::itemAlias('Status', $auLetterUser->status)]);
            }
        }
        return $list;
    }

    /**
     * @return string
     */
    public function showCCRecipientsList($action = 'view')
    {
        $list = '';

        foreach ($this->cCRecipientUser as $auLetterUser) {
            if ($action == 'view') {
                $list .= Html::tag('label', $auLetterUser->auUser?->fullName, ['class' => 'badge badge-secondary mr-2 mb-2', 'title' => AuLetterUser::itemAlias('Status', $auLetterUser->status)]);
            } else {
                $list .= Html::tag('p', $auLetterUser->auUser?->fullName, ['class' => '']);
            }
        }
        return $list;
    }

    /**
     * @return string
     */
    public function showReferenceList()
    {
        $list = '';
        foreach ($this->reference as $auLetterUser) {
            $list .= Html::tag('label', Html::tag('i', '', ['class' => AuLetterUser::itemAlias('StatusIcon', $auLetterUser->status) . ' mr-1']) . $auLetterUser->user?->fullName, ['class' => 'badge badge-info mr-2 mb-2', 'title' => AuLetterUser::itemAlias('Status', $auLetterUser->status)]);
        }
        return $list;
    }

    /**
     * @return int|string|null
     */
    public function getPrintNumber()
    {
        return $this->type == self::TYPE_INPUT ? $this->input_number : $this->number;
    }

    public function getFileTextByOCR(): string
    {
        $client = new GuzzleHttpClient();
        $ocr_text = '';
        try {
            foreach ($this->activity as $activity) {
                $ext = pathinfo($activity->getStorageFileName('file'), PATHINFO_EXTENSION);
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) { // supported file format
                    continue;
                }
                $request = $client->post('https://ocr.hesabrotest.ir/ocr', [
                    'multipart' => [
                        [
                            'name' => 'image',
                            'contents' => $activity->getStorageFileContent('file'),
                            'filename' => $activity->getStorageFileName('file'),
                        ],
                    ],
                ]);

                if ($request->getStatusCode() == 200) {
                    $ocr_text .= $request->getBody()->getContents() . "\n";
                }
            }

        } catch (Exception $e) {
            Yii::error('OCR log:  ' . $e->getMessage() . $e->getTraceAsString(), 'ORC_response');
        }

        return $ocr_text;
    }

    /**
     * @param $skipIfSet
     * @return AuLetter
     */
    public function loadDefaultValues($skipIfSet = true)
    {
        $copyId = Yii::$app->request->get('copy_id');
        if ($copyId && ($copyModel = self::findOne($copyId)) !== null) {
            $this->title = $copyModel->title;
            $this->body = $copyModel->body;
        }
        return parent::loadDefaultValues($skipIfSet);
    }

    /**
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            if ($this->type == self::TYPE_INPUT && $this->input_type == self::INPUT_OUTPUT_SYSTEM) {
                $this->status = self::STATUS_WAIT_CONFIRM;
            } else {
                $this->status = self::STATUS_DRAFT;
            }
        }

        if (in_array($this->getScenario(), [self::SCENARIO_CREATE_INTERNAL, self::SCENARIO_CREATE_INPUT, self::SCENARIO_CREATE_OUTPUT, self::SCENARIO_CREATE_RECORD]) && ($totalStep = count($this->workFlow?->steps ?: [])) > 0) {
            $this->status = self::STATUS_WAIT_CONFIRM; // نامه داراری گردش کار می باشد
            $this->current_step = 0;
            $this->total_step = $totalStep;
        }

        $this->body = !empty(trim((string)$this->body)) ? HtmlPurifier::process($this->body) : NULL;
        return parent::beforeSave($insert);
    }


    /**
     * @param $type
     * @param $code
     * @return false|string|string[]
     */
    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'Type' => [
                self::TYPE_INTERNAL => 'داخلی',
                self::TYPE_INPUT => 'وارده',
                self::TYPE_OUTPUT => 'صادره',
                self::TYPE_RECORD => 'صورت جلسه مالی',
            ],
            'TypeControllers' => [
                self::TYPE_INTERNAL => 'au-letter-internal',
                self::TYPE_INPUT => 'au-letter-input',
                self::TYPE_OUTPUT => 'au-letter-output',
                self::TYPE_RECORD => 'au-letter-record',
            ],
            'Scenario' => [
                self::TYPE_INTERNAL => self::SCENARIO_CREATE_INTERNAL,
                self::TYPE_INPUT => self::SCENARIO_CREATE_INPUT,
                self::TYPE_OUTPUT => self::SCENARIO_CREATE_OUTPUT,
                self::TYPE_RECORD => self::SCENARIO_CREATE_RECORD,
            ],
            'ScenarioConfirm' => [
                self::TYPE_INTERNAL => self::SCENARIO_CONFIRM_AND_SEND_INTERNAL,
                self::TYPE_INPUT => self::SCENARIO_CONFIRM_AND_SEND_INTERNAL,
                self::TYPE_OUTPUT => self::SCENARIO_CONFIRM_AND_SEND_OUTPUT,
                self::TYPE_RECORD => self::SCENARIO_CONFIRM_AND_SEND_RECORD,
            ],
            'Status' => [
                self::STATUS_DRAFT => 'پیش نویس',
                self::STATUS_CONFIRM_AND_SEND => 'ارسال',
                self::STATUS_WAIT_CONFIRM => 'در انتظار تایید',
            ],
            'StatusClass' => [
                self::STATUS_DRAFT => 'secondary',
                self::STATUS_CONFIRM_AND_SEND => 'success',
                self::STATUS_WAIT_CONFIRM => 'info',
            ],
            'InputType' => [
                self::INPUT_OUTPUT_SYSTEM => 'سیستمی',
                self::INPUT_OUTPUT_CUSTOMER => 'ارباب رجوع',
                self::INPUT_OUTPUT_DELIVERY => 'پیک',
                self::INPUT_OUTPUT_EMAIL => 'ایمیل',
                self::INPUT_OUTPUT_FAX => 'فکس',
                self::INPUT_OUTPUT_OTHER => 'سایر',
            ],
            'InputTypeCreate' => [
                self::INPUT_OUTPUT_CUSTOMER => 'ارباب رجوع',
                self::INPUT_OUTPUT_DELIVERY => 'پیک',
                self::INPUT_OUTPUT_EMAIL => 'ایمیل',
                self::INPUT_OUTPUT_FAX => 'فکس',
                self::INPUT_OUTPUT_OTHER => 'سایر',
            ],
            'ViewedIcon' => [
                0 => 'fa fa-envelope',
                1 => 'fa fa-envelope-open',
            ],
            'Notif' => [
                self::NOTIF_AU_LETTER_CONFIRM_AND_START_WORK_FLOW => 'تایید و شروع گردش کار',
                self::NOTIF_AU_LETTER_CONFIRM_AND_NEXT_STEP => 'تایید و ادامه گردش کار',
                self::NOTIF_AU_LETTER_CONFIRM_AND_END_STEPS => 'تایید و اتمام گردش کار',
                self::NOTIF_AU_LETTER_RECEIVE_INPUT => 'نامه های وارده بین سیستمی'
            ],
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by'
            ],
            [
                'class' => TraceBehavior::class,
                'ownerClassName' => self::class
            ],
            [
                'class' => LogBehavior::class,
                'ownerClassName' => self::class,
                'saveAfterInsert' => true
            ],
            [
                'class' => JsonAdditional::class,
                'ownerClassName' => self::class,
                'fieldAdditional' => 'additional_data',
                'AdditionalDataProperty' => [
                    'signatures' => 'Any',
                    'attaches' => 'Any',
                    'files_text' => 'String',
                    'header_text' => 'String',
                    'footer_text' => 'String',
                    'total_step' => 'Integer',
                    'workflow_id_value' => 'Integer'
                ],
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted_at' => time(),
                    'status' => self::STATUS_DELETED
                ],
                'restoreAttributeValues' => [
                    'deleted_at' => null,
                    'status' => self::STATUS_DRAFT,
                ],
                'replaceRegularDelete' => false, // mutate native `delete()` method
                'allowDeleteCallback' => function () {
                    return false;
                },
                'invokeDeleteEvents' => true
            ],
            [
                'class' => NotifBehavior::class,
                'event' => self::NOTIF_AU_LETTER_CONFIRM_AND_START_WORK_FLOW,
                'scenario' => [self::SCENARIO_CONFIRM_AND_START_WORK_FLOW],
            ],
            [
                'class' => NotifBehavior::class,
                'event' => self::NOTIF_AU_LETTER_CONFIRM_AND_NEXT_STEP,
                'scenario' => [self::SCENARIO_CONFIRM_AND_NEXT_STEP],
            ],
            [
                'class' => NotifBehavior::class,
                'event' => self::NOTIF_AU_LETTER_CONFIRM_AND_END_STEPS,
                'scenario' => [self::SCENARIO_CONFIRM_AND_NEXT_STEP],
            ],
            [
                'class' => NotifBehavior::class,
                'event' => self::NOTIF_AU_LETTER_RECEIVE_INPUT,
                'scenario' => [self::SCENARIO_RECEIVE_INPUT],
            ]
        ];
    }

    public function notifUsers(string $event): array
    {
        $users = [];
        if (in_array($this->getScenario(), [self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT, self::SCENARIO_CONFIRM_AND_SEND_INTERNAL])) {
            foreach ($this->recipientUser as $auLetterUser) {
                $users[] = $auLetterUser->user_id;
            }
        }
        if (in_array($this->getScenario(), [self::SCENARIO_CONFIRM_AND_START_WORK_FLOW, self::SCENARIO_CONFIRM_AND_NEXT_STEP])) {
            foreach ($this->getWorkFlowUser()->byStep($this->current_step)->all() as $auLetterUser) {
                /** @var AuLetterUser $auLetterUser */
                $users[] = $auLetterUser->user_id;
            }
        }
        return $users;
    }

    public function notifTitle(string $event): string
    {
        return self::itemAlias('Notif', $event);
    }

    public function notifLink(string $event, ?int $userId): ?string
    {
        return Yii::$app->urlManager->createAbsoluteUrl(['/automation/' . AuLetterBase::itemAlias('TypeControllers', $this->type) . '/view', 'id' => $this->id]);
    }

    public function notifDescription(string $event): ?string
    {
        $content = '';
        if (in_array($this->getScenario(), [self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT, self::SCENARIO_CONFIRM_AND_SEND_INTERNAL])) {
            $content = Html::tag('p', "یک نامه در سیستم ثبت شده است.");
            if ($this->update !== null) {
                $content .= Html::tag('p', 'این نامه توسط "' . $this->update?->fullName . '" در سیستم ثبت شد.');
            }
            $content .= Html::tag('p', 'عنوان نامه : "' . $this->title . '"');
        }
        if (in_array($this->getScenario(), [self::SCENARIO_RECEIVE_INPUT])) {
            $content = Html::tag('p', "یک نامه در سیستم ثبت شده است.");
            $content .= Html::tag('p', 'این نامه توسط "' . $this->showSender() . '" در سیستم ثبت شد.');
            $content .= Html::tag('p', 'عنوان نامه : "' . $this->title . '"');
        }
        if (in_array($this->getScenario(), [self::SCENARIO_CONFIRM_AND_START_WORK_FLOW, self::SCENARIO_CONFIRM_AND_NEXT_STEP])) {
            $content = Html::tag('p', "یک نامه در سیستم ثبت شده است.");
            $content .= Html::tag('p', 'این نامه توسط "' . $this->update?->fullName . '" در سیستم ثبت شد.');
            $content .= Html::tag('p', 'عنوان نامه : "' . $this->title . '"');
        }
        return $content;
    }

    public function notifConditionToSend(string $event): bool
    {
        if ($event === self::NOTIF_AU_LETTER_CONFIRM_AND_END_STEPS && $this->current_step < $this->total_step) {
            return false;
        }

        return true;
    }

    public function notifSmsConditionToSend(string $event): bool
    {
        return true;
    }

    public function notifSmsDelayToSend(string $event): ?int
    {
        return 0;
    }

    public function notifEmailConditionToSend(string $event): bool
    {
        return true;
    }

    public function notifEmailDelayToSend(string $event): ?int
    {
        return 0;
    }
}
