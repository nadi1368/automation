<?php

namespace hesabro\automation\events;

class AuUserEvent extends Event
{
    public $auUser;

    protected static $variableName = 'auUser';
}
