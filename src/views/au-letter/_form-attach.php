<?php


use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelAttach hesabro\automation\models\AuLetterActivity */
?>


<div class="au-letter-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-letter-attach',
        'options' => [
            'enctype' => "multipart/form-data",
        ]
    ]); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($modelAttach, 'file[]')->fileInput(['multiple' => true]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
