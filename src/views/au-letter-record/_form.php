<?php

use hesabro\automation\models\AuLetter;
use hesabro\automation\models\AuUser;
use hesabro\automation\Module;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use hesabro\helpers\widgets\CKEditorWidget;
use hesabro\automation\models\AuFolder;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */
/* @var $form yii\bootstrap4\ActiveForm */

$userClass = Module::getInstance()->user;
?>

<div class="au-letter-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-6">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>


            <div class="col-md-3 date-input">
                <?= $form->field($model, 'date')->widget(MaskedInput::class, [ 'mask' => '9999/99/99']) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'body')->widget(CKEditorWidget::class, [
                    'options' => [
                        'class' => 'form-control description-fields',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
