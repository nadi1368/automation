<?php

namespace hesabro\automation;

use Yii;
use yii\i18n\PhpMessageSource;

/**
 * Class module
 * @package hesabro\automation
 * @author Nader <nader.bahadorii@gmail.com>
 */
class Module extends \yii\base\Module
{
    public string $clientDb = 'clientDb';

    public string | null $client = null;

    public string | null $user = null;

    public string | null $settings = null;

    public string | null $clientSettingsValue = null;

    public string | null $settingsSearch = null;

    public string | null $settingsCategory = null;

    public string | null $userFindUrl = '/user/get-user-list';

    public array | null $employeeRole = ['employee'];

    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t('hesabro/automation/' . $category, $message, $params, $language);
    }
}
