<?php

namespace hesabro\automation\models;

use hesabro\automation\Module;
use Yii;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use hesabro\helpers\behaviors\JsonAdditional;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use backend\modules\master\models\Client;

/**
 * This is the model class for table "automation_client_group".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $additional_data
 * @property int $status
 * @property int $created_at
 * @property int|null $created_by
 * @property int $updated_at
 * @property int|null $updated_by
 * @property int $deleted_at
 * @property int $slave_id
 *
 * @property string $showClients
 */
class AuClientGroup extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const SCENARIO_CREATE = 'create';

    public $clients;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%automation_client_group}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'slave_id'], 'integer'],
            [['title', 'clients'], 'required'],
            [['title'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('app', 'ID'),
            'title' => Module::t('app', 'Title'),
            'additional_data' => Module::t('app', 'Additional Data'),
            'status' => Module::t('app', 'Status'),
            'created_at' => Module::t('app', 'Created At'),
            'created_by' => Module::t('app', 'Created By'),
            'updated_at' => Module::t('app', 'Updated At'),
            'updated_by' => Module::t('app', 'Updated By'),
            'deleted_at' => Module::t('app', 'Deleted At'),
            'slave_id' => Module::t('app', 'Slave ID'),
            'clients' => 'اعضا',
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
     * @return AuClientGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuClientGroupQuery(get_called_class());
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
     * @return string
     */
    public function getShowClients()
    {
        $clients = '';
        foreach (is_array($this->clients) ? $this->clients : [] as $clientsId) {
            if (($client = Client::findOne($clientsId)) !== null) {
                $clients .= '<label class="badge badge-info mr-2 mb-2">' . $client->title . ' </label> ';
            }
        }
        return $clients;
    }

    public static function getClientList()
    {
        $clients = [];
        foreach (self::find()->all() as $group) {
            $clients['g_' . $group->id] = $group->title;
        }
        foreach (Client::itemAlias('ParentBranches') as $clientId=>$clientTitle) {
            $clients[$clientId] = $clientTitle;
        }
        return $clients;
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
                    'clients' => 'IntegerArray',
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
