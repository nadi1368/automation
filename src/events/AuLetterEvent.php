<?php

namespace hesabro\automation\events;

use hesabro\automation\models\AuLetter;
use yii\base\Event;

class AuLetterEvent extends Event
{
    /**
     * @var AuLetter|null
     */
    public $auLetter;

    public static function create($variable): self
    {
        return \Yii::createObject(['class' => self::class, 'auLetter' => $variable]);
    }
}