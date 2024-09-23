<?php

namespace hesabro\automation\traits;

use hesabro\automation\Module;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package hesabro\automation\traits
 */
trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return \Yii::$app->getModule('automation');
    }
}
