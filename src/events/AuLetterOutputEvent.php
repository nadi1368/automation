<?php

namespace hesabro\automation\events;

class AuLetterOutputEvent extends Event
{
    public $auLetterOutput;

    protected static $variableName = 'auLetterOutput';
}