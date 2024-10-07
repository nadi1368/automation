<?php

namespace hesabro\automation\models;


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
}