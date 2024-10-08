<?php


use hesabro\automation\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelAnswer hesabro\automation\models\FormLetterConfirmStep */
?>


<div class="au-letter-form">

    <?php $form = ActiveForm::begin(['id' => 'form-letter-answer']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($modelAnswer, 'answer')->textarea(['rows' => 5]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($modelAnswer, 'signature')->checkbox() ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', 'Save'), ['class' =>'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
