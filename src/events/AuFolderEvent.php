<?php

namespace hesabro\automation\events;

class AuFolderEvent extends Event
{
    public $auFolder;

    protected static $variableName = 'auFolder';
}