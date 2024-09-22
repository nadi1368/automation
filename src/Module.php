<?php

namespace hesabro\automation;

use hesabro\automation\controllers\AuFolderController;
use hesabro\automation\events\AuFolderEvent;
use Yii;
use hesabro\helpers\Module as HesabroHelpersModule;
use yii\i18n\PhpMessageSource;

/**
 * Class module
 * @package backend\modules\automation
 * @author Nader <nader.bahadorii@gmail.com>
 */
class Module extends \yii\base\Module
{
    public string $clientDb = 'clientDb';

    public string | null $client = null;

    public string | null $user = null;

    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t('hesabro/automation/' . $category, $message, $params, $language);
    }
}
