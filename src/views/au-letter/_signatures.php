<?php

use yii\helpers\Html;
use hesabro\automation\models\AuSignatureWithoutMaster;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */
?>
<?php foreach (is_array($model->signatures) ? $model->signatures : [] as $signatureId => $signatureSrc): ?>
    <?php if (($auSignature = AuSignatureWithoutMaster::findOne($signatureId))): ?>
        <?= $auSignature->getSignatureImg(180, 180) ?>
    <?php endif; ?>
<?php endforeach; ?>
<div class="mb-5"></div>