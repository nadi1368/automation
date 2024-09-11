<?php

namespace hesabro\automation\events;

use hesabro\automation\models\AuFolder;

class AuFolderEvent extends Event
{
    private $_auFolder;

    protected static $variableName = 'auFolder';

    const EVENT_BEFORE_SET_ACTIVE = 'beforeSetActive';
    const EVENT_AFTER_SET_ACTIVE = 'afterSetActive';
    const EVENT_BEFORE_SET_INACTIVE = 'beforeSetInactive';
    const EVENT_AFTER_SET_INACTIVE = 'afterSetInactive';
}