<?php

namespace hesabro\automation\models;

use hesabro\automation\Module;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "automation_folder".
 *
 * @property int $id
 * @property string|null $title
 * @property int $start_number
 * @property int $end_number
 * @property int $last_number
 * @property string|null $description
 * @property int $type
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $deleted_at
 * @property int $slave_id
 *
 * @property AuLetter[] $letters
 * @property object $creator
 * @property object $update
 */
class AuFolderBase extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public string $error_msg = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%automation_folder}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['start_number', 'type', 'title'], 'required'],
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id', 'last_number'], 'integer'],
            [['start_number', 'end_number'], 'integer', 'min' => 0, 'max' => 500_000_000],
            [['title'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 128],
            [['type'], 'in', 'range' => array_keys(self::itemAlias('Type'))],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = ['start_number', 'type', 'title', 'end_number', 'description'];
        $scenarios[self::SCENARIO_UPDATE] = ['start_number', 'type', 'title', 'end_number', 'description'];

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
            'start_number' => 'عدد شروع',
            'last_number' => 'آخرین شماره',
            'end_number' => 'عدد پسوند (ثابت)',
            'description' => Module::t('module', 'Description'),
            'type' => Module::t('module', 'Type'),
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
    public function getLetters()
    {
        return $this->hasOne(AuLetter::class, ['folder_id' => 'id']);
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
     * @return AuFolderQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuFolderQuery(get_called_class());
        return $query->active();
    }

    /**
     * @return bool
     */
    public function canCreate()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canUpdate()
    {
        return !$this->getLetters()->exists();
    }

    /**
     * @return bool
     */
    public function canDelete()
    {
        return !$this->getLetters()->exists();
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
     * @return false|int
     */
    public function getAndSaveLastNumber()
    {
        $auFolder = self::find()->andWhere(['id' => $this->id])->limit(1)->findOneSqlForUpdate();
        $auFolder->last_number++;
        if ($auFolder->save(false)) {
            return (int)$auFolder->last_number;
        }
        return false;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_ACTIVE;
        }
        if (in_array($this->scenario, [self::SCENARIO_CREATE, self::SCENARIO_UPDATE])) {
            $this->last_number = $this->start_number;
        }

        return parent::beforeSave($insert);
    }

    const ALL_TYPE = 10;

    public static function getList($type)
    {
        switch ($type){
            case AuLetter::TYPE_INTERNAL:
            case AuLetter::TYPE_INPUT:
            case AuLetter::TYPE_OUTPUT:
            case AuLetter::TYPE_RECORD:
                $list_data = ArrayHelper::map(self::find()->byType([$type,self::ALL_TYPE])->justActive()->all(), 'id', 'title');
                break;

            default :
                $list_data = ArrayHelper::map(self::find()->byType([self::ALL_TYPE])->justActive()->all(), 'id', 'title');
                break;
        }
        return $list_data;
    }
    /**
     * @param $type
     * @param $code
     * @return false|string|string[]
     */
    public static function itemAlias($type, $code = NULL)
    {
        $list_data = [];
        if ($type == 'ListInternal') {
            $list = self::find()->byType([AuLetter::TYPE_INTERNAL,self::ALL_TYPE])->justActive()->all();
            $list_data = ArrayHelper::map($list, 'id', 'title');
        } elseif ($type == 'ListInput') {
            $list = self::find()->byType([AuLetter::TYPE_INPUT,self::ALL_TYPE])->justActive()->all();
            $list_data = ArrayHelper::map($list, 'id', 'title');
        } elseif ($type == 'ListOutput') {
            $list = self::find()->byType([AuLetter::TYPE_OUTPUT,self::ALL_TYPE])->justActive()->all();
            $list_data = ArrayHelper::map($list, 'id', 'title');
        }elseif ($type == 'ListRecord') {
            $list = self::find()->byType([AuLetter::TYPE_RECORD,self::ALL_TYPE])->justActive()->all();
            $list_data = ArrayHelper::map($list, 'id', 'title');
        }
        $_items = [
            'ListInternal' => $list_data,
            'ListInput' => $list_data,
            'ListOutput' => $list_data,
            'Type' => [
                AuLetter::TYPE_INTERNAL => 'داخلی',
                AuLetter::TYPE_INPUT => 'وارده',
                AuLetter::TYPE_OUTPUT => 'صادره',
                self::ALL_TYPE => 'همه',
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
