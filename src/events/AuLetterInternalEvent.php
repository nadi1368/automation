<?php

namespace hesabro\automation\events;

use yii\base\Event;

class AuLetterInternalEvent extends Event
{
    public $auLetterInternal;

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, 'auLetterInternal' => $variable]);
    }
}
