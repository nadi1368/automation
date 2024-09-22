<?php

namespace hesabro\automation\models;

use hesabro\automation\Module;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "automation_user".
 *
 * @property int $id
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $email
 * @property string|null $address
 * @property string|null $description
 * @property int|null $user_id
 * @property string|null $additional_data
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $deleted_at
 * @property int $slave_id
 *
 *
 * @property AuLetterUser[] $letterUsers
 * @property object $creator
 * @property object $update
 * @property string $fullName
 * @property string $fullNameWithNumber
 */
class AuUserBase extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    const SCENARIO_CREATE = 'create';

    public $error_msg = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%automation_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname'], 'required'],
            [['description'], 'string'],
            [['user_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id'], 'integer'],
            [['additional_data'], 'safe'],
            [['firstname', 'lastname', 'phone'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 11],
            [['email'], 'string', 'max' => 128],
            [['address'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'firstname' => 'نام سازمان / شرکت',
            'lastname' => Module::t('module', 'Last Name'),
            'phone' => Module::t('module', 'Phone'),
            'mobile' => Module::t('module', 'Mobile'),
            'email' => Module::t('module', 'Email'),
            'address' => Module::t('module', 'Address'),
            'description' => Module::t('module', 'Description'),
            'user_id' => Module::t('module', 'User ID'),
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
    public function getLetterUser()
    {
        return $this->hasOne(AuLetterUser::class, ['user_id' => 'id']);
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
     * @return AuUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuUserQuery(get_called_class());
        return $query->active();
    }

    public function canUpdate()
    {
        return true;
    }

    public function canDelete()
    {
        return !$this->getLetterUser()->exists();
    }

    public function getFullName()
    {
        return $this->firstname . ' ' . $this->lastname ;
    }

    public function getFullNameWithNumber()
    {
        return $this->firstname . ' ' . $this->lastname . ($this->mobile ? ' - ' . $this->mobile : '') . ($this->phone ? ' - ' . $this->phone : '');
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
        ];
    }

}
