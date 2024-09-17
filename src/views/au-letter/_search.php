<?php

use hesabro\automation\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetterSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="au-letter-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
<div class="card-body">
    <div class="row">
    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'parent_id') ?>

    <?= $form->field($model, 'sender_id') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'folder_id') ?>

    <?php // echo $form->field($model, 'body') ?>

    <?php // echo $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'input_number') ?>

    <?php // echo $form->field($model, 'input_type') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'additional_data') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'deleted_at') ?>

    <?php // echo $form->field($model, 'slave_id') ?>

		<div class="col align-self-center text-right">
			<?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
			<?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
		</div>
	</div>
</div>
    <?php ActiveForm::end(); ?>

</div>
