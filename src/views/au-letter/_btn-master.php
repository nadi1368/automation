<?php

use hesabro\automation\models\AuLetter;
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
<?= $model->canConfirmAndSend() ? Html::a(Module::t('module', 'Confirm And Send'),
    'javascript:void(0)', [
        'title' => Module::t('module', 'Confirm And Send'),
        'data-title' => Module::t('module', 'Confirm And Send'),
        'class' => 'btn btn-secondary  mr-1',
        'data-size' => 'modal-lg',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['confirm-and-send', 'id' => $model->id]),
        'data-reload-pjax-container-on-show' => 0,
        'data-reload-pjax-container' => 'p-jax-letter',
    ]) : '' ?>
<?= $model->canConfirmAndStartWorkFlow() ? Html::a(Module::t('module', 'Confirm And Start work flow'), ['confirm-and-start-work-flow', 'id' => $model->id], [
    'class' => 'btn btn-secondary  mr-1',
    'data' => [
        'confirm' => Module::t('module', 'Are you sure?'),
        'method' => 'post',
    ],
]) : '' ?>
<?= $model->canConfirmInCurrentStep() ? Html::a(Module::t('module', 'Confirm Step'),
    'javascript:void(0)', [
        'title' => Module::t('module', 'Confirm Step'),
        'data-title' => Module::t('module', 'Confirm Step'),
        'class' => 'btn btn-secondary  mr-1',
        'data-size' => 'modal-lg',
        'data-toggle' => 'modal',
        'data-target' => '#modal-pjax',
        'data-url' => Url::to(['confirm-step', 'id' => $model->id]),
        'data-reload-pjax-container-on-show' => 0,
        'data-reload-pjax-container' => 'p-jax-letter',
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
            'data-pjax' => '0',
        ],
    ];
endforeach;

$itemsPrint[] = [
    'label' => 'بدون سربرگ',
    'url' => ['print', 'id' => $model->id],
    'encode' => false,
    'linkOptions' => [
        'data-pjax' => '0',
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
            'data-pjax' => '0',
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
<?= Html::a('نسخه برداری',
    [
        AuLetter::itemAlias('TypeControllers', $model->type) . '/create', 'copy_id' => $model->id, 'type' => $model->type == AuLetter::TYPE_OUTPUT && $model->input_type == AuLetter::INPUT_OUTPUT_SYSTEM ? AuLetter::INPUT_OUTPUT_SYSTEM : null
    ],
    [
        'class' => 'btn btn-secondary mr-1 ',
        'title' => 'نسخه برداری از متن این نامه برای ایجاد نامه جدید',
    ]
);
?>
<?= Html::a(Module::t('module', 'Log'),
    ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => AuLetter::class],
    [
        'class' => 'btn btn-secondary showModalButton mr-1 ',
        'title' => Module::t('module', 'Logs'),
        'data-size' => 'modal-xl'
    ]
);
?>

