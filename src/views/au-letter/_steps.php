<?php

use common\models\Order;
use hesabro\automation\models\AuLetterUser;
use hesabro\automation\models\AuWorkFlowStep;
use hesabro\automation\models\WorkFlowJsonData;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */

$items=$model->getWorkFlowUser()->groupBy(['step'])->orderBy(['step' => SORT_ASC])->all();
$countItems=count($items);
?>
<div class="row bs-wizard ">
    <?php $currentStep = 1; ?>

    <?php foreach ($items as $workFlowUser): ?>
        <?php
        /** @var AuLetterUser $workFlowUser */
        $status_class = "disabled";
        if ($model->current_step == $workFlowUser->step) {
            $status_class = "active";
        } elseif ($model->current_step > $workFlowUser->step) {
            $status_class = "complete";
        }
        ?>

        <div class="col bs-wizard-step <?= $status_class ?>">
            <div class="text-center bs-wizard-stepnum">
                <?= $workFlowUser->title . ' (' . AuWorkFlowStep::itemAlias('OperationType', $workFlowUser->operation_type) . ')' ?>
            </div>
            <?php if($countItems>1): ?>
            <div class="progress">
                <div class="progress-bar"></div>
            </div>
            <span href="#" class="bs-wizard-dot"></span>
            <?php endif; ?>
            <div class="text-center">
                <?= $model->showWorkFlowUserList($workFlowUser->step) ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

