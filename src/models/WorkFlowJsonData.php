<?php

namespace hesabro\automation\models;


use hesabro\automation\Module;

/**
 * Class WorkFlowJsonData
 * @package hesabro\automation\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class WorkFlowJsonData extends BaseModelJsonData
{
    public ?string $title = null;
    public ?int $operation_type = null;
    public ?array $users = null;

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