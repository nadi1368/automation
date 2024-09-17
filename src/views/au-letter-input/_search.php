<?php

use hesabro\automation\models\AuFolder;
use hesabro\automation\models\AuLetter;
use hesabro\automation\Module;
use kartik\select2\Select2;
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
            <div class="col-md-2">
                <?= $form->field($model, 'number') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'title') ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'folder_id')->widget(Select2::class, [
                    'data' => AuFolder::itemAlias('ListInput'),
                    'options' => ['placeholder' => Module::t('module', "Search")],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'input_type')->dropdownList(AuLetter::itemAlias('InputTypeCreate'), ['prompt' => Module::t('module', 'Select')]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'input_number') ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'status')->dropdownList(AuLetter::itemAlias('Status'), ['prompt' => Module::t('module', 'Select')]) ?>
            </div>

            <div class="col align-self-center text-right">
                <?= Html::submitButton(Module::t('module', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Module::t('module', 'Reset'), ['class' => 'btn btn-secondary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
