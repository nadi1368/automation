<?php

namespace hesabro\automation;

use hesabro\automation\models\AuFolder;
use hesabro\automation\models\AuFolderQuery;
use hesabro\automation\models\AuFolderSearch;
use hesabro\automation\models\AuLetter;
use hesabro\automation\models\AuLetterActivity;
use hesabro\automation\models\AuLetterActivityQuery;
use hesabro\automation\models\AuLetterActivityWithoutSlave;
use hesabro\automation\models\AuLetterClient;
use hesabro\automation\models\AuLetterClientQuery;
use hesabro\automation\models\AuLetterClientWithoutSlave;
use hesabro\automation\models\AuLetterQuery;
use hesabro\automation\models\AuLetterSearch;
use hesabro\automation\models\AuLetterUser;
use hesabro\automation\models\AuLetterUserQuery;
use hesabro\automation\models\AuLetterWithoutSlave;
use hesabro\automation\models\AuPrintLayout;
use hesabro\automation\models\AuPrintLayoutQuery;
use hesabro\automation\models\AuPrintLayoutSearch;
use hesabro\automation\models\AuSignature;
use hesabro\automation\models\AuSignatureQuery;
use hesabro\automation\models\AuSignatureSearch;
use hesabro\automation\models\AuSignatureWithoutMaster;
use hesabro\automation\models\AuUser;
use hesabro\automation\models\AuUserQuery;
use hesabro\automation\models\AuUserSearch;
use hesabro\automation\models\FormLetterAnswer;
use hesabro\automation\models\FormLetterAttach;
use hesabro\automation\models\FormLetterReference;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    private $modelMap = [
        AuFolder::class => AuFolder::class,
        AuFolderQuery::class => AuFolderQuery::class,
        AuFolderSearch::class => AuFolderSearch::class,
        AuLetter::class => AuLetter::class,
        AuLetterActivity::class => AuLetterActivity::class,
        AuLetterActivityQuery::class => AuLetterActivityQuery::class,
        AuLetterActivityWithoutSlave::class => AuLetterActivityWithoutSlave::class,
        AuLetterClient::class => AuLetterClient::class,
        AuLetterClientQuery::class => AuLetterClientQuery::class,
        AuLetterClientWithoutSlave::class => AuLetterClientWithoutSlave::class,
        AuLetterQuery::class => AuLetterQuery::class,
        AuLetterSearch::class => AuLetterSearch::class,
        AuLetterUser::class => AuLetterUser::class,
        AuLetterUserQuery::class => AuLetterUserQuery::class,
        AuLetterWithoutSlave::class => AuLetterWithoutSlave::class,
        AuPrintLayout::class => AuPrintLayout::class,
        AuPrintLayoutQuery::class => AuPrintLayoutQuery::class,
        AuPrintLayoutSearch::class => AuPrintLayoutSearch::class,
        AuSignature::class => AuSignature::class,
        AuSignatureQuery::class => AuSignatureQuery::class,
        AuSignatureSearch::class => AuSignatureSearch::class,
        AuSignatureWithoutMaster::class => AuSignatureWithoutMaster::class,
        AuUser::class => AuUser::class,
        AuUserQuery::class => AuUserQuery::class,
        AuUserSearch::class => AuUserSearch::class,
        FormLetterAnswer::class => FormLetterAnswer::class,
        FormLetterAttach::class => FormLetterAttach::class,
        FormLetterReference::class => FormLetterReference::class
    ];

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->params['bsVersion'] = 4;
        $moduleId = $app->params['automationModuleId'] ?? 'automation';

        if (($module = $app->getModule($moduleId)) instanceof Module) {
            $this->modelMap = array_merge($this->modelMap, $module->modelMap);

            foreach ($this->modelMap as $class => $definition) {
                Yii::$container->set($class, $definition);
                $modelName = is_array($definition) ? $definition['class'] : $definition;
                $module->modelMap[$class] = $modelName;
            }
        }
    }
}
