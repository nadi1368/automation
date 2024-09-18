<?php

namespace hesabro\automation\events;

use yii\base\Event;

class AuSignatureEvent extends Event
{
    public $auSignature;

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, 'auSignature' => $variable]);
    }
}
