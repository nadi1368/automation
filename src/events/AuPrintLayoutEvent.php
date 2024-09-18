<?php

namespace hesabro\automation\events;

use yii\base\Event;

class AuPrintLayoutEvent extends Event
{
    public $auPrintLayout;

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, 'auPrintLayout' => $variable]);
    }
}