<?php

use hesabro\automation\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuPrintLayoutSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="au-print-layout-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
<div class="card-body">
    <div class="row">
    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'logo') ?>

    <?= $form->field($model, 'additional_data') ?>

    <?= $form->field($model, 'status') ?>

		<div class="col align-self-center text-right">
			<?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
		</div>
	</div>
</div>
    <?php ActiveForm::end(); ?>

</div>
