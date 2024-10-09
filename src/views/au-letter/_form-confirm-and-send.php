<?php


use hesabro\automation\models\AuFolder;
use hesabro\automation\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */
?>


<div class="au-letter-form">

    <?php $form = ActiveForm::begin(['id' => 'form-letter-confirm-and-send']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                    <?= $form->field($model, 'folder_id')->widget(Select2::class, [
                        'data' => AuFolder::getList($model->type),
                        'options' => ['placeholder' => Module::t('module', "Search")],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', 'Save'), ['class' =>'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
