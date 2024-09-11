<?php

use hesabro\automation\models\AuLetterActivity;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */
/* @var $activity hesabro\automation\models\AuLetterActivity */
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="timeline timeline-right">
                    <?php foreach ($model->getActivity()->orderBy(['id' => SORT_DESC])->all() as $activity): ?>
                        <li class="timeline-item">
                            <div class="timeline-badge <?= AuLetterActivity::itemAlias('TypeClass', $activity->type) ?>"><?= AuLetterActivity::itemAlias('Type', $activity->type) ?></div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4 class="timeline-title"><?= $activity->creator->fullName ?></h4>
                                    <p><small class="text-muted">
                                            <i class="fa fa-clock-o"></i> <?= Yii::$app->jdate->date("l d F Y H:i:s", $activity->created_at) ?>
                                        </small></p>
                                </div>
                                <div class="timeline-body">
                                    <?= $activity->getBody(); ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>