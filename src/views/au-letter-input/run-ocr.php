<?php

use hesabro\helpers\widgets\CKEditorWidget;
use hesabro\automation\Module;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/** @var yii\web\View $this */
/** @var hesabro\automation\models\AuLetter $model */

?>
<div class="au-letter-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-letter-run-ocr',
        'options' => [
            'enctype' => "multipart/form-data",
        ]
    ]); ?>
    <div class="card-body">
        <div class="row">
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
        <?= Html::submitButton(Module::t('module', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>