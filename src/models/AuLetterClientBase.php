<?php

namespace hesabro\automation\models;

use hesabro\automation\Module;
use Yii;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "automation_letter_client".
 *
 * @property int $id
 * @property int $letter_id
 * @property int $client_id
 * @property int $type
 * @property string|null $title
 * @property string|null $additional_date
 * @property int $status
 *
 * @property AuLetter $letter
 * @property object $client
 * @mixin SoftDeleteBehavior
 */
class AuLetterClientBase extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_WAIT_SEND = 1;
    const STATUS_WAIT_VIEW = 2;
    const STATUS_VIEWED = 3;
    const STATUS_ANSWERED = 4;

    const TYPE_RECIPIENTS = 1; // گیرنده

    const SCENARIO_CREATE = 'create';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%automation_letter_client}}';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['letter_id', 'client_id', 'type'], 'required'],
            [['letter_id', 'client_id', 'type', 'status'], 'integer'],
            [['additional_date'], 'safe'],
            [['title'], 'string', 'max' => 64],
            [['letter_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuLetter::class, 'targetAttribute' => ['letter_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('module', 'ID'),
            'letter_id' => Module::t('module', 'Letter ID'),
            'client_id' => Module::t('module', 'Client ID'),
            'type' => Module::t('module', 'Type'),
            'title' => Module::t('module', 'Title'),
            'additional_date' => Module::t('module', 'Additional Date'),
            'status' => Module::t('module', 'Status'),
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
    public function getClient()
    {
        return $this->hasOne(Module::getInstance()->client, ['id' => 'client_id']);
    }

    /**
     * {@inheritdoc}
     * @return AuLetterClientQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuLetterClientQuery(get_called_class());
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
            $this->status = self::STATUS_WAIT_SEND;
        }
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
            'Status'=>[
                self::STATUS_WAIT_SEND => 'در انتظار ارسال',
                self::STATUS_WAIT_VIEW => 'در انتظار مشاهده',
                self::STATUS_VIEWED => 'مشاهده شده',
                self::STATUS_ANSWERED => 'پاسخ داده شده',
            ],
            'StatusIcon'=>[
                self::STATUS_WAIT_SEND => 'fa fa-clock',
                self::STATUS_WAIT_VIEW => 'fa fa-envelope',
                self::STATUS_VIEWED => 'fa fa-envelope-open',
                self::STATUS_ANSWERED => 'fa fa-reply',
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
//                [
//                    'class' => JsonAdditional::class,
//                    'ownerClassName' => self::class,
//                    'fieldAdditional'=>'additional_data',
//                    'AdditionalDataProperty'=>[
//                        'confirm_user_id' => 'Integer',
//                        'confirm_auto' => 'Boolean',
//                    ],
//                ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'status' => self::STATUS_DELETED
                ],
                'restoreAttributeValues' => [
                    'status' => self::STATUS_WAIT_VIEW,
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
