<?php

use hesabro\automation\models\AuLetter;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'اتوماسیون اداری';
$this->params['breadcrumbs'][] = $this->title;
 ?>
<div class="row">
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
        <div class="card border-bottom border-cyan">
            <div class="card-body">
                <div class="d-flex no-block align-items-center">
                    <div>
                        <h2><?= AuLetter::find()->byUser(Yii::$app->user->id)->byStatus(AuLetter::STATUS_CONFIRM_AND_SEND)->count() ?></h2>
                        <h6 class="text-cyan"><?= Html::a(Module::t('module', 'My Letters'), ['/automation/au-letter/index'], ['class' => 'text-cyan']) ?></h6>
                    </div>
                    <div class="ml-auto">
                        <span class="text-cyan display-6"><i class="fal fa-folder"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
        <div class="card border-bottom border-cyan">
            <div class="card-body">
                <div class="d-flex no-block align-items-center">
                    <div>
                        <h2><?= AuLetter::find()->byUserDontView(Yii::$app->user->id)->byStatus(AuLetter::STATUS_CONFIRM_AND_SEND)->count() ?></h2>
                        <h6 class="text-cyan"><?= Html::a(Module::t('module', 'My Letters Wait View'), ['/automation/au-letter/index'], ['class' => 'text-cyan']) ?></h6>
                    </div>
                    <div class="ml-auto">
                        <span class="text-cyan display-6"><i class="fal fa-envelope"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
        <div class="card border-bottom border-cyan">
            <div class="card-body">
                <div class="d-flex no-block align-items-center">
                    <div>
                        <h2><?= AuLetter::find()->waitInput()->count() ?></h2>
                        <h6 class="text-cyan"><?= Html::a(Module::t('module', 'My Letters Input Wait View'), ['/automation/au-letter-input/index', 'AuLetterSearch[status]'=>AuLetter::STATUS_WAIT_CONFIRM], ['class' => 'text-cyan']) ?></h6>
                    </div>
                    <div class="ml-auto">
                        <span class="text-cyan display-6"><i class="fal fa-envelope"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>