<?php

namespace hesabro\automation\models;

use hesabro\automation\Module;
use hesabro\helpers\traits\ModelHelper;
use yii\base\Model;

class AuWorkFlowStep extends Model
{
    use ModelHelper;

    const OPERATION_TYPE_AND = 1;

    const OPERATION_TYPE_OR = 2;

    public bool $isNewRecord = true;

    public mixed $step = null;

    public mixed $operation_type = null;

    public array $users = [];

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['step', 'operation_type', 'users'], 'required'],
            ['step', 'number'],
            ['operation_type', 'in', 'range' => array_keys(self::itemAlias('OperationType'))],
            ['users', 'exist', 'allowArray' => true, 'targetClass' => Module::getInstance()->user, 'targetAttribute' => 'id']
        ]);
    }

    public function attributeLabels()
    {
        return [
            'step' => Module::t('module', 'Step'),
            'operation_type' => Module::t('module', 'Operation Type'),
            'users' => Module::t('module', 'User ID'),
        ];
    }

    public static function itemAlias($type, $code = null)
    {
        $items = [
            'OperationType' => [
                self::OPERATION_TYPE_AND => Module::t('module', 'All Persons'),
                self::OPERATION_TYPE_OR => Module::t('module', 'Only One Person'),
            ]
        ];

        return isset($code) ? ($items[$type][$code] ?? false) : $items[$type] ?? false;
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
}
