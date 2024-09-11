<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuUser */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="au-user-form">

    <?php $form = ActiveForm::begin(['id' => 'form-au-user']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'firstname')->textInput(['maxlength' => true])->hint('اجباری') ?>
            </div>

            <div class="col-md-12"></div>

            <div class="col-md-4">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true])->hint('اختیاری') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'mobile')->textInput(['maxlength' => true])->hint('اختیاری') ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true])->hint('اختیاری') ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'address')->textInput(['maxlength' => true])->hint('اختیاری') ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 6])->hint('اختیاری') ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
