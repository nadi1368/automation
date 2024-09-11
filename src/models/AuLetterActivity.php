<?php

namespace hesabro\automation\models;

use backend\modules\storage\behaviors\StorageUploadBehavior;
use backend\modules\storage\models\StorageFiles;
use common\behaviors\CdnUploadFileBehavior;
use hesabro\automation\Module;
use Yii;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use hesabro\helpers\behaviors\JsonAdditional;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "automation_letter_activity".
 *
 * @property int $id
 * @property int $letter_id
 * @property int $type
 * @property string|null $json
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $slave_id
 *
 * @property object $creator
 * @property AuLetter $letter
 * @mixin  StorageUploadBehavior
 */
class AuLetterActivity extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    const SCENARIO_ATTACH = 'attach';

    const TYPE_REFERENCE = 1; //ارجاع
    const TYPE_ANSWER = 2; // پاسخ
    const TYPE_ATTACH = 3; // فایل
    const TYPE_SIGNATURE = 4; //امضا

    /** Additional Data */
    public $referenceUserId; // کاربر ارجاع شده
    public $answer; // متن پاسخ
    public $file; // فایل
    public $signatureId; // امضا

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'automation_letter_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['letter_id', 'type'], 'required'],
            [['file'], 'required', 'on' => self::SCENARIO_ATTACH],
            [['letter_id', 'type', 'status', 'created_at', 'created_by', 'slave_id'], 'integer'],
            [['json'], 'safe'],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuLetter::class, 'targetAttribute' => ['letter_id' => 'id']],
            ['file', 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'pdf', 'mp4', 'xlsx', 'xls'], 'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'application/pdf', 'video/mp4', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'], 'maxSize' => 2 * 1024 * 1024, 'on' => [self::SCENARIO_ATTACH], 'maxFiles' => 10],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_ATTACH] = ['file'];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'letter_id' => Module::t('module', 'Letter ID'),
            'type' => Module::t('module', 'Type'),
            'json' => Module::t('module', 'Json'),
            'status' => Module::t('module', 'Status'),
            'created_at' => Module::t('module', 'Created At'),
            'created_by' => Module::t('module', 'Created By'),
            'slave_id' => Module::t('module', 'Slave ID'),
            'file' => Module::t('module', 'Attach File'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLetter()
    {
        return $this->hasOne(AuLetter::class, ['id' => 'letter_id']);
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
     * @return AuLetterActivityQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuLetterActivityQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return true;
    }

    public function getBody()
    {
        switch ($this->type) {
            case self::TYPE_REFERENCE:
                $auLetterUser = AuLetterUser::find()->byLetter($this->letter_id)->byUser($this->referenceUserId)->limit(1)->one();
                return $auLetterUser !== null ? Html::tag('label', Html::tag('i', '', ['class' => AuLetterUser::itemAlias('StatusIcon', $auLetterUser->status) . ' mr-1']) . $auLetterUser->user?->fullName, ['class' => 'badge badge-info mr-2 mb-2', 'title' => AuLetterUser::itemAlias('Status', $auLetterUser->status)]) : '';
            case self::TYPE_ANSWER:
                return nl2br($this->answer);
            case self::TYPE_ATTACH:
                if ($fileUrl = $this->getFileUrl('file')) ;
                {
                    if ($this->isImage()) {
                        return Html::a(Html::img($fileUrl, ['width' => 200, 'height' => 200, 'class' => "rounded border img-thumbnail"]), $fileUrl, ['data-pjax' => 0]);
                    } else {
                        return Html::a('دانلود فایل پیوست', $fileUrl, ['data-pjax' => 0, 'class' => 'btn btn-info']);
                    }
                }

            case self::TYPE_SIGNATURE:
                $auSignature = AuSignature::findOne($this->signatureId);
                return $auSignature !== null ? $auSignature->signatureImg . '<br />' . $auSignature->title : '';

        }
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        $search = ['jpg', 'jpeg', 'png'];
        $fileName = $this->getFileStorageName();
        $exp = '/'
            . implode('|', array_map('preg_quote', $search))
            . ('/i');
        return is_string($fileName) && preg_match($exp, $fileName);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
            $this->created_at = time();
            $this->created_by = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
    }

    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'Type' =>
                [
                    self::TYPE_REFERENCE => 'ارجاع',
                    self::TYPE_ANSWER => 'پاسخ',
                    self::TYPE_ATTACH => 'پیوست',
                    self::TYPE_SIGNATURE => 'امضا',
                ],
            'TypeClass' =>
                [
                    self::TYPE_REFERENCE => 'info',
                    self::TYPE_ANSWER => 'success',
                    self::TYPE_ATTACH => 'primary',
                    self::TYPE_SIGNATURE => 'primary',
                ],
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function behaviors()
    {
        return [
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
                    'referenceUserId' => 'Integer',
                    'answer' => 'String',
                    'signatureId' => 'Integer',
                ],
            ],

            [
                'class' => CdnUploadFileBehavior::class,
                'model_class' => 'letters',
                'allowed_mime_types' => 'application,image',
            ],
            [
                'class' => StorageUploadBehavior::class,
                'modelType' => StorageFiles::MODEL_TYPE_AUTOMATION_LETTER,
                'attributes' => ['file'],
                'accessFile' => StorageFiles::ACCESS_PRIVATE,
                'scenarios' => [self::SCENARIO_ATTACH],
                'sharedWith' => function (self $model) {
            AuLetter::find();
                    $auLetter = AuLetterWithoutSlave::findOne($model->letter_id);
                    return $auLetter !== null && $auLetter->type == AuLetter::TYPE_OUTPUT && $auLetter->input_type == AuLetter::INPUT_OUTPUT_SYSTEM ? array_keys(ArrayHelper::map($auLetter->recipientClientWithoutSlave, 'client_id', 'client_id')) : [];
                }
            ],
        ];
    }

}
