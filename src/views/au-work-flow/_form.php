<?php

use hesabro\automation\bundles\JqueryUIAsset;
use hesabro\automation\models\AuWorkFlow;
use hesabro\automation\models\AuWorkFlowStep;
use hesabro\automation\Module;
use hesabro\helpers\components\iconify\Iconify;
use hesabro\helpers\widgets\DynamicFormWidget;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model AuWorkFlow */
/* @var $form yii\bootstrap4\ActiveForm */

$classUser = Module::getInstance()->user;

JqueryUIAsset::register($this);
?>

<div class="au-work-follow-form">

    <?php $form = ActiveForm::begin(['id'=>'form-au-work-flow']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'letter_type')
                    ->dropDownList(AuWorkFlow::itemAlias('LetterType'), [
                        'value' => $model->isNewRecord ? null : (int) $model->letter_type,
                        'prompt' => Module::t('module', "Select")
                    ]);
                ?>
            </div>
            <hr />

            <div id="steps" class="col-12 mt-4">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'workflow_steps_dynamic_form',
                    'widgetBody' => '.workflow-steps',
                    'widgetItem' => '.workflow-step',
                    'limit' => 10,
                    'min' => 0,
                    'insertButton' => '.add-step',
                    'deleteButton' => '.remove-step',
                    'model' => $model->steps[0] ?? null,
                    'formId' => 'form-au-work-flow',
                    'formFields' => [
                        'step', 'operation_type', 'users'
                    ],
                ]); ?>

                <div class="w-100 d-flex items-center justify-content-between gap-2">
                    <label class="font-16"><?= Module::t('module', 'Steps') ?></label>
                    <button type="button" class="btn btn-success add-step btn-xs" style="border-radius: 4px !important;">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <div class="workflow-steps">
                    <?php
                    /** @var AuWorkFlowStep $step */
                    foreach ($model->steps as $stepIndex => $step):
                    ?>
                        <div class="workflow-step row mb-2">
                            <div class="col-12 col-md-1 d-flex align-items-center" style="gap: 6px; cursor: n-resize;">
                                <span class="font-18"><?= Iconify::getInstance()->icon('ph:arrows-out-line-vertical-fill') ?></span>
                                <span class="workflow-step-counter font-18"><?= $step->step ?></span>
                                <?= $form->field($step, "[$stepIndex]step")->hiddenInput()->label(false) ?>
                            </div>
                            <div class="col-12 col-md-3">
                                <?= $form->field($step, "[$stepIndex]operation_type")
                                    ->dropDownList(AuWorkFlowStep::itemAlias('OperationType')) ?>
                            </div>
                            <div class="col-12 col-md-7">
                                <?= $form->field($step, "[$stepIndex]users")->widget(Select2::class, [
                                    'initValueText' => $step->users && is_array($step->users) ? ArrayHelper::map($classUser::find()->andWhere(['IN', 'id', $step->users])->all(), "id", "fullName") : [],
                                    'options' => ['placeholder' => Module::t('module', "Search"), 'dir' => 'rtl', 'multiple' => true],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 3,
                                        'language' => [
                                            'errorLoading' => new JsExpression("function () { return 'خطا در جستجوی اطلاعات'; }"),
                                            'inputTooShort' => new JsExpression("function () { return 'لطفا تایپ نمایید'; }"),
                                            'loadingMore' => new JsExpression("function () { return 'بارگیری بیشتر'; }"),
                                            'noResults' => new JsExpression("function () { return 'نتیجه ای یافت نشد.'; }"),
                                            'searching' => new JsExpression("function () { return 'در حال جستجو...'; }"),
                                            'maximumSelected' => new JsExpression("function () { return 'حداکثر انتخاب شده'; }"),
                                        ],
                                        'ajax' => [
                                            'url' => Url::to(Module::getInstance()->userFindUrl),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                        ],
                                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                        'templateResult' => new JsExpression('function(data) { return data.text; }'),
                                        'templateSelection' => new JsExpression('function (data) { return data.text; }'),
                                    ],
                                ]); ?>
                            </div>
                            <div class="col-12 col-md-1 d-flex align-items-center justify-content-end pt-2">
                                <button type="button" class="btn btn-danger remove-step btn-xs" style="border-radius: 4px !important;">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(<<<JS
$(document).ready(function () {
    let sortSteps = () => {
        const steps = $('.workflow-steps > .workflow-step')
        for (let step = 1; step <= steps.length; step++) {
            $(steps[step - 1]).find('input[name$="[step]"]').val(step)
            $(steps[step - 1]).find('.workflow-step-counter').text(`\${step}.`)
        }
    }

    const stepsDynamicForm = $('.workflow_steps_dynamic_form');
    stepsDynamicForm.on('afterInsert', sortSteps);
    stepsDynamicForm.on('afterDelete', sortSteps);
    $('.workflow-steps').sortable({ update: sortSteps });
})
JS, View::POS_END);
?>
