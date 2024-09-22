<?php

namespace hesabro\automation\models;

use Yii;
use hesabro\automation\Module;
use hesabro\changelog\behaviors\LogBehavior;
use hesabro\errorlog\behaviors\TraceBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "automation_letter_customer".
 *
 * @property int $id
 * @property int $letter_id
 * @property int $user_id
 * @property int $type
 * @property string|null $title
 * @property int $status
 * @property int $slave_id
 *
 * @property AuLetter $letter
 * @property AuUser $auUser
 * @mixin SoftDeleteBehavior
 */
class AuLetterUserBase extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_WAIT_VIEW = 1;
    const STATUS_VIEWED = 2;
    const STATUS_ANSWERED = 3;

    const TYPE_RECIPIENTS = 1; // گیرنده
    const TYPE_CC_RECIPIENTS = 2; // رونوشت
    const TYPE_REFERENCE = 3; // ارجاع

    const SCENARIO_CREATE = 'create';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%automation_letter_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['letter_id', 'user_id', 'type'], 'required'],
            [['letter_id', 'user_id', 'type', 'status', 'slave_id'], 'integer'],
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
            'user_id' => Module::t('module', 'Customer ID'),
            'type' => Module::t('module', 'Type'),
            'title' => Module::t('module', 'Title'),
            'status' => Module::t('module', 'Status'),
            'slave_id' => Module::t('module', 'Slave ID'),
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
    public function getUser()
    {
        return $this->hasOne(Module::getInstance()->user, ['id' => 'user_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuUser()
    {
        return $this->hasOne(AuUser::class, ['id' => 'user_id']);
    }



    /**
     * {@inheritdoc}
     * @return AuLetterUserQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new AuLetterUserQuery(get_called_class());
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
            $this->status = self::STATUS_WAIT_VIEW;
        }
        return parent::beforeSave($insert);
    }

    public static function itemAlias($type, $code = NULL)
    {

        $_items = [
            'Status'=>[
                self::STATUS_WAIT_VIEW => 'در انتظار مشاهده',
                self::STATUS_VIEWED => 'مشاهده شده',
                self::STATUS_ANSWERED => 'پاسخ داده شده',
            ],
            'StatusIcon'=>[
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
