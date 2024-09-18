<?php

namespace hesabro\automation\events;

use yii\base\Event;

class AuFolderEvent extends Event
{
    public $auFolder;
    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, 'auFolder' => $variable]);
    }
}