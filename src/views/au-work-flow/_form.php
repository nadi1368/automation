<?php

use hesabro\automation\models\AuWorkFlow;
use hesabro\automation\Module;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuWorkFlow */
/* @var $form yii\bootstrap4\ActiveForm */

$classUser = Module::getInstance()->user;
?>

<div class="au-work-follow-form">

    <?php $form = ActiveForm::begin(['id'=>'form-au-work-flow']); ?>
    <div class="card-body">
        <div class="row">

            <div class="col-md-4">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'operation_type')->dropDownList(AuWorkFlow::itemAlias('OperationType'), ['prompt' => Module::t('module', "Select...")]); ?>
            </div>

            <div class="col-md-12">
                <?= $form->field($model, 'users')->widget(Select2::classname(), [
                    'initValueText' => $model->users && is_array($model->users) ? ArrayHelper::map($classUser::find()->andWhere(['IN', 'id', $model->users])->all(), "id", "fullName") : [],
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
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton($model->isNewRecord ? Module::t('module', 'Create') : Module::t('module', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
