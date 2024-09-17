<?php

use hesabro\automation\Module;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$userClass = Module::getInstance()->user;

/* @var $this yii\web\View */
/* @var $modelReference hesabro\automation\models\FormLetterReference */
?>


<div class="au-letter-form">

    <?php $form = ActiveForm::begin(['id' => 'form-letter-reference']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($modelReference, 'user_id')->widget(Select2::class, [
                    'data' => $userClass::getUserWithRoles(['employee']),
                    'options' => [
                        'dir' => 'rtl',
                        'placeholder' => '',
                    ],
                ]); ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', 'Save'), ['class' =>'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
