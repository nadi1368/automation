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

    public string|null $user = null;

    public array $modelMap = [];

    public array $eventMap = [
        AuFolderController::class => AuFolderEvent::class
    ];

    public function getModel(string|array $namespace): string|array|null
    {
        if (is_array($namespace)) {
            return array_map(fn ($item) => $this->getModel($item), $namespace);
        }

        return $this->modelMap[$namespace] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->registerTranslation();

        $this->setModules([
            'helpers' => [
                'class' => HesabroHelpersModule::class,
            ]
        ]);
    }

    private function registerTranslation(): void
    {
        Yii::$app->i18n->translations['hesabro/automation'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@hesabro/automation/messages',
            'sourceLanguage' => 'en-US',
            'fileMap' => [
                'hesabro/automation/module' => 'module.php'
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t('hesabro/automation/' . $category, $message, $params, $language);
    }
}
