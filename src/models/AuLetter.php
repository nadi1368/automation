<?php

namespace hesabro\automation\models;

use console\job\CreateLetterJob;
use hesabro\automation\Module;
use Yii;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use hesabro\helpers\behaviors\JsonAdditional;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
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
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $deleted_at
 * @property int $slave_id
 *
 * @property AuLetterUser[] $user
 * @property AuLetterUser[] $recipientUser
 * @property AuLetterClient[] $recipientClient
 * @property AuLetterClientWithoutSlave[] $recipientClientWithoutSlave
 * @property AuLetterUser[] $cCRecipientUser
 * @property AuLetterUser[] $reference
 * @property AuLetterActivity[] $activity
 * @property AuFolder $folder
 * @property object $creator
 * @property object $update
 * @property string $printNumber
 * @property bool $viewed
 */
class AuLetter extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_DRAFT = 1; // پیش نویش
    const STATUS_CONFIRM_AND_SEND = 2; // تایید و ارسال
    const STATUS_WAIT_CONFIRM = 3; // در انتظار تایید نامه های وارده بین سیستمی

    const TYPE_INTERNAL = 1; // داخلی
    const TYPE_INPUT = 2; // وارده
    const TYPE_OUTPUT = 3; // صادره


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
    const SCENARIO_RECEIVE_INPUT = 'RECEIVE_INPUT';
    const SCENARIO_CREATE_OUTPUT = 'create_output';

    public $error_msg = '';
    public $recipients; // گیرندگان
    public $cc_recipients; // رونوشت

    // ----- ADDITIONAL DATA ------
    public $attaches; // فایل های پیوست
    public $signatures;
    public $files_text;
    // ----------------------------
    
    private $_viewd = null;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'automation_letter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'sender_id', 'type', 'folder_id', 'number', 'input_type', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id'], 'integer'],
            [['type', 'title', 'folder_id', 'date', 'recipients'], 'required', 'on' => self::SCENARIO_CREATE_INTERNAL],
            [['type', 'title', 'folder_id', 'date', 'recipients', 'sender_id', 'input_type'], 'required', 'on' => self::SCENARIO_CREATE_INPUT],
            [['type', 'title', 'folder_id', 'date', 'sender_id', 'recipients', 'input_type'], 'required', 'on' => self::SCENARIO_CREATE_OUTPUT],
            [['folder_id', 'recipients'], 'required', 'on' => self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT],
            [['recipients', 'cc_recipients'], 'safe', 'on' => [self::SCENARIO_CREATE_INTERNAL, self::SCENARIO_CREATE_OUTPUT, self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT]],
            [['body'], 'string'],
            [['title'], 'string', 'max' => 64],
            [['input_number'], 'string', 'max' => 32],
            [['date'], 'string', 'max' => 10],
            [['input_number'], 'default', 'value' => ''],
            [['input_type'], 'default', 'value' => 1],
            [['folder_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuFolder::class, 'targetAttribute' => ['folder_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE_INTERNAL] = ['title', 'folder_id', 'date', 'body', 'recipients', 'cc_recipients'];
        $scenarios[self::SCENARIO_CREATE_INPUT] = ['title', 'folder_id', 'date', 'body', 'recipients', 'cc_recipients', 'sender_id', 'input_type', 'input_number'];
        $scenarios[self::SCENARIO_CREATE_OUTPUT] = ['title', 'folder_id', 'date', 'body', 'sender_id', 'recipients', 'cc_recipients', 'input_type'];
        $scenarios[self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT] = ['folder_id', 'recipients', 'cc_recipients'];
        $scenarios[self::SCENARIO_CONFIRM_AND_SEND_INTERNAL] = ['!status'];
        $scenarios[self::SCENARIO_CONFIRM_AND_SEND_OUTPUT] = ['!status'];
        $scenarios[self::SCENARIO_RECEIVE_INPUT] = ['!type'];

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
        return $this->hasMany(Module::getInstance()->getModel(AuLetterUser::class), ['letter_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipientUser()
    {
        return $this->hasMany(Module::getInstance()->getModel(AuLetterUser::class), ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_RECIPIENTS]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipientClient()
    {
        return $this->hasMany(Module::getInstance()->getModel(AuLetterClient::class), ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_RECIPIENTS]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecipientClientWithoutSlave()
    {
        return $this->hasMany(Module::getInstance()->getModel(AuLetterClientWithoutSlave::class), ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_RECIPIENTS]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCCRecipientUser()
    {
        return $this->hasMany(Module::getInstance()->getModel(AuLetterUser::class), ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_CC_RECIPIENTS]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReference()
    {
        return $this->hasMany(Module::getInstance()->getModel(AuLetterUser::class), ['letter_id' => 'id'])->andWhere(['type' => AuLetterUser::TYPE_REFERENCE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasMany(Module::getInstance()->getModel(AuLetterActivity::class), ['letter_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFolder()
    {
        return $this->hasOne(Module::getInstance()->getModel(AuFolder::class), ['id' => 'folder_id']);
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
        return $this->status == self::STATUS_DRAFT;
    }

    public function canDelete()
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function canConfirmAndSend()
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function canPrint()
    {
        return $this->status == self::STATUS_CONFIRM_AND_SEND;
    }

    public function canConfirmAndReceive()
    {
        return $this->status == self::STATUS_WAIT_CONFIRM && $this->type == self::TYPE_INPUT;
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
        $auLetterActivity = Module::getInstance()->getModel(AuLetterActivity::class);
        return (int)$this->getActivity()->andWhere([$auLetterActivity::tableName() . '.type' => AuLetterActivity::TYPE_ATTACH])->count();
    }

    /**
     * @return bool
     * @throws yii\db\Exception
     */
    public function createRecipientsInternal(): bool
    {
        foreach (is_array($this->recipients) ? $this->recipients : [] as $userId) {
            $class = Module::getInstance()->getModel(AuLetterUser::class);
            $auLetterUser = new $class(['type' => AuLetterUser::TYPE_RECIPIENTS]);
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
        foreach (is_array($this->recipients) ? $this->recipients : [] as $clientId) {
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

        $this->recipients = is_array($this->recipients) ? $this->recipients : [];
        $deleted_recipients_ids = array_diff($old_recipients, $this->recipients);
        if (!empty($deleted_recipients_ids)) {
            foreach ($deleted_recipients_ids as $userId) {
                if (($model = AuLetterClient::find()->andWhere(['client_id' => $userId, 'letter_id' => $this->id, 'type' => AuLetterClient::TYPE_RECIPIENTS])->limit(1)->one()) !== null && !$model->softDelete()) {
                    return false;
                }
            }
        }
        foreach (array_diff($this->recipients, $old_recipients) as $userId) {
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
            $flag = $this->save(false);
            if ($this->type == self::TYPE_OUTPUT && $this->input_type == self::INPUT_OUTPUT_SYSTEM) {
                $flag = $flag && $this->sendToAnotherClient();
            }
            return $flag;
        }
        $this->error_msg = 'خطا در شماره گذاری نامه.لطفا مجددا تلاش نمایید.';
        return false;
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
    public function sendToAnotherClient(): bool
    {
        foreach ($this->recipientClient as $letterClient) {
            Yii::$app->queueSql->push(new CreateLetterJob([
                "slaveId" => $letterClient->client_id,
                "letterId" => $this->id,
            ]));
        }

        return true;
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
    }

    /**
     * @return string
     */
    public function showSender()
    {
        $sender = '';
        if ($this->sender_id) {
            if ($this->type==self::TYPE_INPUT) {
                if($this->input_type == self::INPUT_OUTPUT_SYSTEM)
                {
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
            foreach ($this->activity as $activity) { /** @var AuLetterActivity $activity */
                $ext = pathinfo($activity->storageFile->file_name, PATHINFO_EXTENSION);
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) { // supported file format
                    continue;
                }
                $request = $client->post('https://ocr.hesabrotest.ir/ocr', [
                    'multipart' => [
                            [
                                'name' => 'image',
                                'contents' => $activity->storageFile->fileContent,
                                'filename' => $activity->storageFile->file_name,
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
            ],
            'TypeControllers' => [
                self::TYPE_INTERNAL => 'au-letter-internal',
                self::TYPE_INPUT => 'au-letter-input',
                self::TYPE_OUTPUT => 'au-letter-output',
            ],
            'Scenario' => [
                self::TYPE_INTERNAL => self::SCENARIO_CREATE_INTERNAL,
                self::TYPE_INPUT => self::SCENARIO_CREATE_INPUT,
                self::TYPE_OUTPUT => self::SCENARIO_CREATE_OUTPUT,
            ],
            'Status' => [
                self::STATUS_DRAFT => 'پیش نویس',
                self::STATUS_CONFIRM_AND_SEND => 'ارسال',
                self::STATUS_WAIT_CONFIRM => 'در انتظار تایید',
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
        ];
    }
}
