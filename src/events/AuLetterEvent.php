<?php

namespace hesabro\automation\events;

class AuLetterEvent extends Event
{
    private $_auLetter;

    protected static $variableName = 'auLetter';

    public const EVENT_BEFORE_CONFIRM_AND_SEND = 'beforeConfirmAndSend';

    public const EVENT_AFTER_CONFIRM_AND_SEND = 'afterConfirmAndSend';

    public const EVENT_BEFORE_REFERENCE = 'beforeReference';

    public const EVENT_AFTER_REFERENCE = 'afterReference';

    public const EVENT_BEFORE_ANSWER = 'beforeAnswer';

    public const EVENT_AFTER_ANSWER = 'afterAnswer';

    public const EVENT_BEFORE_ATTACH = 'beforeAttach';

    public const EVENT_AFTER_ATTACH = 'afterAttach';

    public const EVENT_BEFORE_SIGNATURE = 'beforeSignature';

    public const EVENT_AFTER_SIGNATURE = 'afterSignature';
}