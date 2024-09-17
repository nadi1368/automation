<?php

namespace hesabro\automation\events;

use yii\base\Event as BaseEvent;

class Event extends BaseEvent
{
    protected static $variableName = 'variable';

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, self::$variableName => $variable]);
    }
}