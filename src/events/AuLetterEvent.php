<?php

namespace hesabro\automation\events;

use hesabro\automation\models\AuLetter;

class AuLetterEvent extends Event
{
    /**
     * @var AuLetter|null
     */
    public $auLetter;

    protected static $variableName = 'auLetter';
}