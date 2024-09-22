<?php

namespace hesabro\automation;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\i18n\PhpMessageSource;

class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $this->registerTranslation($app);
        $app->params['bsVersion'] = 4;
    }

    private function registerTranslation(Application $app): void
    {
        $app->i18n->translations['hesabro/automation*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@hesabro/automation/messages',
            'sourceLanguage' => 'en-US',
            'fileMap' => [
                'hesabro/automation/module' => 'module.php'
            ],
        ];
    }
}
