<?php

namespace hesabro\automation\models;

use Yii;
use hesabro\automation\Module;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use hesabro\helpers\behaviors\JsonAdditional;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "automation_work_flow".
 *
 * @property int $id
 * @property string|null $title
 * @property int|null $letter_type نوع نامه
 * @property int|null $operation_type AND OR
 * @property string|null $additional_data
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $deleted_at
 * @property int $slave_id
 * @property-read string|null $letterTypeTitle
 * @property-read object $update
 * @property-read object $creator
 * @property-read AuWorkFlowStep[] $stepsByOrder
 */
class AuWorkFlowBase extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;

    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 2;

    const SCENARIO_CREATE = 'create';

    const AU_LETTER_TYPE_ALL = 0;

    public mixed $steps = [];

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
            [['letter_type', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id'], 'integer'],
            [['title', 'steps'], 'required'],
            [['title'], 'string', 'max' => 64],
            ['letter_type', 'in', 'range' => array_keys(self::itemAlias('LetterType'))],
            ['steps', 'validateStep']
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
            'additional_data' => Module::t('module', 'Additional Data'),
            'status' => Module::t('module', 'Status'),
            'created_at' => Module::t('module', 'Created At'),
            'created_by' => Module::t('module', 'Created By'),
            'updated_at' => Module::t('module', 'Updated At'),
            'updated_by' => Module::t('module', 'Updated By'),
            'deleted_at' => Module::t('module', 'Deleted At'),
            'slave_id' => Module::t('module', 'Slave ID')
        ];
    }

    public function validateStep($attribute)
    {
        foreach ($this->steps as $step) {
            if (!($step instanceof AuWorkFlowStep)) {
                $this->addError($attribute, Module::t('module', 'Invalid Value'));
                break;
            }
        }
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

    public function getLetterTypeTitle(): ?string
    {
        return self::itemAlias('LetterType', $this->letter_type ?: self::AU_LETTER_TYPE_ALL);
    }

    /**
     * @return AuWorkFlowStep[]
     */
    public function getStepsByOrder(): array
    {
        $steps = $this->steps ?: [];
        usort($steps, function(AuWorkFlowStep $a, AuWorkFlowStep$b) {
            if ($a->step == $b->step) {
                return 0;
            }

            return ($a->step < $b->step) ? -1 : 1;
        });

        return $steps;
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

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
            $this->letter_type = $this->letter_type == self::AU_LETTER_TYPE_ALL ? null : $this->letter_type;
        }
        return parent::beforeSave($insert);
    }

    public static function itemAlias($type, $code = null)
    {
        $items = [
            'LetterType' => [
                self::AU_LETTER_TYPE_ALL => Module::t('module', 'All'),
                ...AuLetter::itemAlias('Type'),
            ],
            'InternalFlow' => ArrayHelper::map(AuWorkFlow::find()->andWhere([
                'OR',
                ['=', 'letter_type', AuLetter::TYPE_INTERNAL],
                ['IS', 'letter_type', null],
            ])->all(), 'id', 'title'),
            'InputFlow' => ArrayHelper::map(AuWorkFlow::find()->andWhere([
                'OR',
                ['=', 'letter_type', AuLetter::TYPE_INPUT],
                ['IS', 'letter_type', null],
            ])->all(), 'id', 'title'),
            'OutputFlow' => ArrayHelper::map(AuWorkFlow::find()->andWhere([
                'OR',
                ['=', 'letter_type', AuLetter::TYPE_OUTPUT],
                ['IS', 'letter_type', null],
            ])->all(), 'id', 'title'),
        ];

        return isset($code) ? ($items[$type][$code] ?? false) : $items[$type] ?? false;
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
                    'steps' => 'ClassArray::' . AuWorkFlowStep::class,
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
