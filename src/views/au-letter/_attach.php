<?php

use yii\helpers\Html;
use hesabro\automation\models\AuLetterActivityWithoutSlave;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */
?>
<?php foreach (is_array($model->attaches) ? $model->attaches : [] as $attachId ): ?>
    <?php if (($auAttach = AuLetterActivityWithoutSlave::findOne($attachId))): ?>
        <?= $auAttach->getBody() ?>
    <?php endif; ?>
<?php endforeach; ?>
<div class="mb-5"></div>