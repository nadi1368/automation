<?php

use common\models\Order;
use hesabro\automation\models\WorkFlowJsonData;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */
?>
<div class="row bs-wizard ">
    <?php foreach (is_array($model->steps) ? $model->steps : [] as $stepWorkFlow): ?>
        <?php
    /** @var WorkFlowJsonData $stepWorkFlow */
        $status_class = "disabled";

        ?>
        <div class="col bs-wizard-step <?= $status_class ?>">
            <div class="text-center bs-wizard-stepnum">
                <?= $stepWorkFlow->title ?>
            </div>
            <div class="progress">
                <div class="progress-bar"></div>
            </div>
            <span href="#" class="bs-wizard-dot"></span>
            <div class="text-center">
                <?= $stepWorkFlow->showUsersList() ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

