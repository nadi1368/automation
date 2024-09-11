<?php

use hesabro\automation\models\AuLetter;
use hesabro\automation\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use hesabro\automation\models\AuFolder;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuFolder */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="afolder-form">

    <?php $form = ActiveForm::begin(['id'=>'form-au-folder']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'type')->dropdownList(AuFolder::itemAlias('Type'), ['prompt'=>Module::t('module','Select')]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'start_number')->textInput() ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'end_number')->textInput() ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'description') ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
