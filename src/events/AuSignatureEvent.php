<?php

namespace hesabro\automation\events;

class AuSignatureEvent extends Event
{
    public $auSignature;

    protected static $variableName = 'auSignature';
}
