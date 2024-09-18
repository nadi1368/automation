<?php

namespace hesabro\automation\events;

use yii\base\Event;

class AuLetterInputEvent extends Event
{
    public $auLetterInput;

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, 'auLetterInput' => $variable]);
    }
}