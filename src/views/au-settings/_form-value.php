<?php

use hesabro\automation\Module;
use kartik\file\FileInput;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Settings */
/* @var $form yii\bootstrap4\ActiveForm */

?>

<div class="settings-form">

    <?php $form = ActiveForm::begin(['id'=>'form-setting']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?php if($model->field != 'image'): ?>
                    <?= $model->generateForm($form); ?>
                <?php else: ?>
                    <?= $form->field($model, "photo")->widget(FileInput::class, $model->generateForm($form)) ?>
                <?php endif; ?>

                <?php if($model->hasErrors()): ?>
                    <p class="text-danger"><?= $model->getFirstError('client_value') ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
            <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn  submit btn-success' : 'btn btn-primary ']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
