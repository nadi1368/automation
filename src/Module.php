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

    public string | null $settings = null;

    public string | null $settingsSearch = null;

    public string | null $settingsCategory = null;

    public string | null $userFindUrl = '/user/get-user-list';

    public string | null $employeeRole = 'employee';

    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t('hesabro/automation/' . $category, $message, $params, $language);
    }
}
