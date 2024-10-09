<?php

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
            <?= $form->field($model, 'id') ?>

            <?= $form->field($model, 'title') ?>

            <?= $form->field($model, 'letter_type') ?>

            <?= $form->field($model, 'order_by') ?>

            <?= $form->field($model, 'operation_type') ?>

            <div class="col align-self-center text-right">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
