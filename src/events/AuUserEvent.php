<?php

namespace hesabro\automation\events;

use yii\base\Event;

class AuUserEvent extends Event
{
    public $auUser;

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, 'auUser' => $variable]);
    }
}
