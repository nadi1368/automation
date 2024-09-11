<?php

namespace hesabro\automation\models;

use backend\modules\storage\behaviors\StorageUploadBehavior;
use backend\modules\storage\models\StorageFiles;
use common\behaviors\CdnUploadFileBehavior;
use hesabro\automation\Module;
use hesabro\helpers\behaviors\JsonAdditional;
use Yii;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Html;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "automation_signature".
 *
 * @property int $id
 * @property string|null $title
 * @property int $user_id
 * @property string|null $signature
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $deleted_at
 * @property int $slave_id
 *
 *
 * @property string $signatureImg
 * @property object $user
 */
class AuSignature extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /** AdditionalData */
    public $users_other;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'automation_signature';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'signature', 'user_id'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id', 'user_id'], 'integer'],
            [['title'], 'required', 'on' => [self::SCENARIO_UPDATE]],
            [['title'], 'string', 'max' => 64],
            [['user_id'], 'unique'],
            [['users_other'], 'each', 'rule' => ['integer']],
            ['signature', 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'svg'], 'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'], 'maxSize' => 1 * 1024 * 1024, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['user_id'], 'exist', 'targetClass' => Module::getInstance()->user, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['title', 'signature', 'user_id', 'users_other'];
        $scenarios[self::SCENARIO_UPDATE] = ['title', 'signature', 'user_id', 'users_other'];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'title' => Module::t('module', 'Title'),
            'user_id' => Module::t('module', 'User ID'),
            'signature' => Module::t('module', 'Signature'),
            'status' => Module::t('module', 'Status'),
            'created_at' => Module::t('module', 'Created At'),
            'created_by' => Module::t('module', 'Created By'),
            'updated_at' => Module::t('module', 'Updated At'),
            'updated_by' => Module::t('module', 'Updated By'),
            'deleted_at' => Module::t('module', 'Deleted At'),
            'slave_id' => Module::t('module', 'Slave ID'),
            'users_other' => 'سایر کاربران که دسترسی دارند',
        ];
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return AuSignatureQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuSignatureQuery(get_called_class());
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

    /**
     * @return bool
     */
    public function canActive(): bool
    {
        return $this->status == self::STATUS_INACTIVE;
    }

    /**
     * @return bool
     */
    public function canInActive(): bool
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function canUse() : bool
    {
        $users = [$this->user_id];
        if (is_array($this->users_other)) {
            $users = array_merge($users, $this->users_other);
        }
        if (in_array(Yii::$app->user->id, $users)) {
            return true;
        }
        return false;
    }

    /**
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getSignatureImg(int $width = 120, int $height = 120): string
    {
        if ($imgSrc = $this->getFileUrl('signature')) {
            return Html::img($imgSrc, ['width' => $width, 'height' => $height, 'class' => "rounded-circle"]);
        }
        return '';
    }

    /**
     * @return string
     */
    public function showUsersList()
    {
        $users = '';
        foreach (is_array($this->users_other) ? $this->users_other : [] as $userId) {
            $userClass = Module::getInstance()->user;
            if (($user = $userClass::findOne($userId)) !== null) {
                $users .= $user->getLink('badge badge-info mr-1 mb-1 pull-right');
            }
        }
        return $users;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
        }
        return parent::beforeSave($insert);
    }

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
                    'users_other' => 'IntegerArray',
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
                    'status' => self::STATUS_ACTIVE,
                ],
                'replaceRegularDelete' => false, // mutate native `delete()` method
                'allowDeleteCallback' => function () {
                    return false;
                },
                'invokeDeleteEvents' => true
            ],

            [
                'class' => CdnUploadFileBehavior::class,
                'model_class' => 'signature',
                'allowed_mime_types' => 'application,image',
            ],
            [
                'class' => StorageUploadBehavior::class,
                'modelType' => StorageFiles::MODEL_TYPE_AUTOMATION_SIGNATURE,
                'attributes' => ['signature'],
                'accessFile' => StorageFiles::ACCESS_PRIVATE,
                'scenarios' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE],
                'sharedWith' => function (self $model) {
                    return array_keys(Client::itemAlias('ParentBranches'));
                }
            ],
        ];
    }

}
