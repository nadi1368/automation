<?php

namespace hesabro\automation\models;

use Yii;
use hesabro\automation\Module;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use hesabro\helpers\behaviors\JsonAdditional;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "automation_work_flow".
 *
 * @property int $id
 * @property string|null $title
 * @property int|null $letter_type نوع نامه
 * @property int|null $order_by
 * @property int|null $operation_type AND OR
 * @property string|null $additional_data
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $deleted_at
 * @property int $slave_id
 */
class AuWorkFlowBase extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const SCENARIO_CREATE = 'create';

    const OPERATION_TYPE_AND = 1;
    const OPERATION_TYPE_OR = 2;

    /** AdditionalData */
    public $users;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%automation_work_flow}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['letter_type', 'order_by', 'operation_type', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id'], 'integer'],
            [['letter_type', 'title', 'operation_type', 'users'], 'required'],
            [['title'], 'string', 'max' => 64],
            [['users'], 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'title' => Module::t('module', 'Title'),
            'letter_type' => Module::t('module', 'Letter Type'),
            'order_by' => Module::t('module', 'Order By'),
            'operation_type' => Module::t('module', 'Operation Type'),
            'additional_data' => Module::t('module', 'Additional Data'),
            'status' => Module::t('module', 'Status'),
            'created_at' => Module::t('module', 'Created At'),
            'created_by' => Module::t('module', 'Created By'),
            'updated_at' => Module::t('module', 'Updated At'),
            'updated_by' => Module::t('module', 'Updated By'),
            'deleted_at' => Module::t('module', 'Deleted At'),
            'slave_id' => Module::t('module', 'Slave ID'),
            'users' => 'اشخاص این مرحله',
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
     * {@inheritdoc}
     * @return AuWorkFlowQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuWorkFlowQuery(get_called_class());
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
     * @return string
     */
    public function showUsersList()
    {
        $users = '';
        foreach (is_array($this->users) ? $this->users : [] as $userId) {
            $userClass = Module::getInstance()->user;
            if (($user = $userClass::findOne($userId)) !== null) {
                $users .= $user->getLink('badge badge-info mr-1 mb-1 pull-right');
            }
        }
        return $users;
    }

    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'OperationType' => [
                self::OPERATION_TYPE_AND => 'همه اشخاص',
                self::OPERATION_TYPE_OR => 'تنها یکی از اشخاص',
            ]
        ];
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function afterSoftDelete()
    {
        self::updateAllCounters(['order_by' => -1], ['AND', ['letter_type' => $this->letter_type], ['>', 'order_by', $this->order_by]]);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
            $this->order_by = (int)self::find()->byLetterType($this->letter_type)->max('order_by') + 1;
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
                    'users' => 'IntegerArray',
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
        ];
    }

}
