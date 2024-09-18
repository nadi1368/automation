<?php

namespace hesabro\automation\events;

use yii\base\Event;

class AuLetterOutputEvent extends Event
{
    public $auLetterOutput;

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, 'auLetterOutput' => $variable]);
    }
}