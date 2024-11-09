<?php

use hesabro\automation\models\AuWorkFlow;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuWorkFlowSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="au-work-follow-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-3">
                <?= $form->field($model, 'title') ?>
            </div>

            <div class="col-12 col-md-3">
                <?= $form->field($model, 'letter_type')->dropDownList(AuWorkFlow::itemAlias('LetterType')) ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
