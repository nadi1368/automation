<?php

namespace hesabro\automation\events;

class AuLetterInputEvent extends Event
{
    public $auLetterInput;

    protected static $variableName = 'auLetterInput';
}