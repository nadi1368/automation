<?php

use hesabro\automation\models\AuPrintLayout;
use hesabro\automation\models\AuSignature;
use hesabro\automation\Module;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */

?>
<?= $model->canUpdate() ? Html::a(Module::t('module', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-secondary  mr-1']) : '' ?>
<?= $model->canDelete() ? Html::a(Module::t('module', 'Delete'), ['delete', 'id' => $model->id], [
    'class' => 'btn btn-danger  mr-1',
    'data' => [
        'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
        'method' => 'post',
    ],
]) : '' ?>
<?= $model->canConfirmAndSend() ? Html::a(Module::t('module', 'Confirm And Send'), ['confirm-and-send', 'id' => $model->id], [
    'class' => 'btn btn-secondary  mr-1',
    'data' => [
        'confirm' => Module::t('module', 'Are you sure?'),
        'method' => 'post',
    ],
]) : '' ?>
<?= $model->canReference() ? Html::a(Module::t('module', 'Letter Reference'),
    'javascript:void(0)', [
        'title' => Module::t('module', 'Letter Reference'),
        'data-title' => Module::t('module', 'Letter Reference'),
        'class' => 'btn btn-secondary  mr-1',
        'data-size' => 'modal-lg',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['reference', 'id' => $model->id]),
        'data-reload-pjax-container-on-show' => 0,
        'data-reload-pjax-container' => 'p-jax-letter',
    ]) : '' ?>
<?= $model->canAnswer() ? Html::a(Module::t('module', 'Letter Answer'),
    'javascript:void(0)', [
        'title' => Module::t('module', 'Letter Answer'),
        'data-title' => Module::t('module', 'Letter Answer'),
        'class' => 'btn btn-secondary  mr-1',
        'data-size' => 'modal-lg',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['answer', 'id' => $model->id]),
        'data-reload-pjax-container-on-show' => 0,
        'data-reload-pjax-container' => 'p-jax-letter',
    ]) : '' ?>
<?= $model->canAttach() ? Html::a(Module::t('module', 'Letter Attach'),
    'javascript:void(0)', [
        'title' => Module::t('module', 'Letter Attach'),
        'data-title' => Module::t('module', 'Letter Attach'),
        'class' => 'btn btn-secondary  mr-1',
        'data-size' => 'modal-lg',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['attach', 'id' => $model->id]),
        'data-reload-pjax-container-on-show' => 0,
        'data-reload-pjax-container' => 'p-jax-letter',
    ]) : '' ?>
<?php
$itemsPrint = [];
foreach (AuPrintLayout::find()->justActive()->all() as $print):
    $itemsPrint[] = [
        'label' => $print->title,
        'url' => ['print', 'id' => $model->id, 'print_id' => $print->id],
        'encode' => false,
        'linkOptions' => [
            'data-pjax' => 'false',
        ],
    ];
endforeach;

$itemsPrint[] = [
    'label' => 'بدون سربرگ',
    'url' => ['print', 'id' => $model->id],
    'encode' => false,
    'linkOptions' => [
        'data-pjax' => 'false',
    ],
];
?>
<?= $model->canPrint() && count($itemsPrint) > 0 ? ButtonDropdown::widget([
    'buttonOptions' => ['class' => 'btn btn-info  mr-1 dropdown-toggle', 'title' => Module::t('module', 'Actions')],
    'encodeLabel' => false,
    'label' => Module::t('module', 'Print'),
    'options' => ['class' => ''],
    'dropdown' => [
        'items' => $itemsPrint,
    ],
]) : '' ?>
<?php
$itemsSignature = [];
foreach (AuSignature::find()->justActive()->all() as $signature):
    $itemsSignature[] = [
        'label' => $signature->title,
        'url' => ['signature', 'id' => $model->id, 'signature_id' => $signature->id],
        'encode' => false,
        'linkOptions' => [
            'data-pjax' => 'false',
        ],
    ];
endforeach;
?>
<?= $model->canSignature() && count($itemsSignature) > 0 ? ButtonDropdown::widget([
    'buttonOptions' => ['class' => 'btn btn-secondary  mr-1 dropdown-toggle', 'title' => Module::t('module', 'Actions')],
    'encodeLabel' => false,
    'label' => Module::t('module', 'Letter Signature'),
    'options' => ['class' => ''],
    'dropdown' => [
        'items' => $itemsSignature,
    ],
]) : '' ?>

