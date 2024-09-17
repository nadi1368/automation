<?php

use hesabro\automation\Module;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var hesabro\automation\models\AuLetter $model */

?>
<?= $model->canConfirmAndReceive() ? Html::a(Module::t('module', 'Letter Confirm And Receive'),
    'javascript:void(0)', [
        'title' => Module::t('module', 'Letter Confirm And Receive'),
        'data-title' => Module::t('module', 'Letter Confirm And Receive'),
        'class' => 'btn btn-secondary  mr-1',
        'data-size' => 'modal-lg',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['confirm-and-receive', 'id' => $model->id]),
        'data-reload-pjax-container-on-show' => 0,
        'data-reload-pjax-container' => 'p-jax-letter',
    ]) : '' ?>

<?= $model->canRunOCR() ? Html::a('اجرا OCR',
    'javascript:void(0)', [
        'title' => 'اجرا OCR',
        'data-title' => 'اجرا OCR',
        'class' => 'btn btn-success  mr-1',
        'data-size' => 'modal-lg',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['run-ocr', 'id' => $model->id]),
        'data-reload-pjax-container-on-show' => 0,
        'data-reload-pjax-container' => 'p-jax-letter',
    ]) : '' ?>

