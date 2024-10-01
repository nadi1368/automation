<?php

use hesabro\automation\Module;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use backend\modules\master\models\Client;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuClientGroup */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="au-client-group-form">

    <?php $form = ActiveForm::begin(['id' => 'form-client-group']); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'clients')->widget(Select2::class, [
                    'data' => Client::itemAlias('ParentBranches'),
                    'options' => [
                        'dir' => 'rtl',
                        'multiple' => true
                    ],
                ]); ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
