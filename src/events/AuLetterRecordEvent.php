<?php

namespace hesabro\automation\events;

use yii\base\Event;

class AuLetterRecordEvent extends Event
{
    public $auLetterRecord;

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, 'auLetterRecord' => $variable]);
    }
}
