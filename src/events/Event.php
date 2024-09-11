<?php

namespace hesabro\automation\events;

use yii\base\Event as BaseEvent;

class Event extends BaseEvent
{
    const EVENT_BEFORE_CREATE = 'beforeCreate';

    const EVENT_AFTER_CREATE = 'afterCreate';

    const EVENT_BEFORE_UPDATE = 'beforeUpdate';

    const EVENT_AFTER_UPDATE = 'afterUpdate';

    const EVENT_BEFORE_DELETE = 'beforeDelete';

    const EVENT_AFTER_DELETE = 'afterDelete';

    protected static $variableName = 'variable';

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, self::$variableName => $variable]);
    }
}