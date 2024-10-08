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
    public ?array $letter_users = null;

    /**
     * @return string
     */
    public function showUsersList()
    {
        $users = '';
        foreach (is_array($this->users) ? $this->users : [] as $userId) {
            $list .= Html::tag('label', Html::tag('i', '', ['class' => AuLetterUser::itemAlias('StatusIcon', $auLetterUser->status) . ' mr-1']) . $auLetterUser->auUser?->fullName, ['class' => 'badge badge-info mr-2 mb-2', 'title' => AuLetterUser::itemAlias('Status', $auLetterUser->status)]);

            $userClass = Module::getInstance()->user;
            if (($user = $userClass::findOne($userId)) !== null) {
                $users .= $user->getLink('badge badge-info mr-1 mb-1 pull-right');
            }
        }
        return $users;
    }

}