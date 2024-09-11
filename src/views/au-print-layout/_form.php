<?php

use hesabro\automation\models\AuPrintLayout;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\widgets\CKEditorWidget;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuPrintLayout */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="au-print-layout-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-print-layout',
        'options' => [
            'enctype' => "multipart/form-data",
        ]
    ]); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-6">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'logo')->fileInput() ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'headerText')->widget(CKEditorWidget::class, [
                    'options' => [
                        'class' => 'form-control description-fields',
                    ],
                ]) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'footerText')->widget(CKEditorWidget::class, [
                    'options' => [
                        'class' => 'form-control description-fields',
                    ],
                ]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'headerHeight')->textInput() ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'footerHeight')->textInput() ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'marginTop')->textInput() ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'marginRight')->textInput() ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'marginBottom')->textInput() ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'marginLeft')->textInput() ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'signaturePosition')->dropdownList(AuPrintLayout::itemAlias('TextAlignTitle')) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'size')->dropdownList(AuPrintLayout::itemAlias('Size')) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'fontTitle')->dropdownList(AuPrintLayout::itemAlias('Fonts'), ['prompt'=>Module::t('module','Select')]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'fontCCRecipients')->dropdownList(AuPrintLayout::itemAlias('Fonts'), ['prompt'=>Module::t('module','Select')]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'showTitleHeader')->checkbox() ?>
            </div>


        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
