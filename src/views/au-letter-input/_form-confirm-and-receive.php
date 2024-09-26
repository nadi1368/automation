<?php

use hesabro\automation\models\AuUser;
use hesabro\automation\Module;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use hesabro\automation\models\AuFolder;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */
/* @var $form yii\bootstrap4\ActiveForm */

$userClass = Module::getInstance()->user;
?>

<div class="au-letter-form">

    <?php $form = ActiveForm::begin(['id' => 'form-letter-confirm-and-receive']); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'folder_id')->widget(Select2::class, [
                    'data' => AuFolder::itemAlias('ListInput'),
                    'options' => ['placeholder' => Module::t('module', "Search")],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'recipients')->widget(Select2::class, [
                    'data' => $userClass::getUserWithRoles(['employee']),
                    'options' => [
                        'dir' => 'rtl',
                        'multiple' => true
                    ],
                ]); ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'cc_recipients')->widget(Select2::class, [
                    'initValueText' => $model->cc_recipients && is_array($model->cc_recipients) ? ArrayHelper::map(AuUser::find()->andWhere(['IN', 'id', $model->cc_recipients])->all(), "id", "fullName") : [],
                    'options' => ['placeholder' => Module::t('module', "Search"), 'multiple' => true, 'dir' => 'rtl'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'multiple' => true,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'خطا در جستجوی اطلاعات'; }"),
                            'inputTooShort' => new JsExpression("function () { return 'لطفا تایپ نمایید'; }"),
                            'loadingMore' => new JsExpression("function () { return 'بارگیری بیشتر'; }"),
                            'noResults' => new JsExpression("function () { return 'نتیجه ای یافت نشد.'; }"),
                            'searching' => new JsExpression("function () { return 'در حال جستجو...'; }"),
                            'maximumSelected' => new JsExpression("function () { return 'حداکثر انتخاب شده'; }"),
                        ],
                        'ajax' => [
                            'url' => Url::to(['au-user/get-user-list']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(user) { return user.text_show; }'),
                        'templateSelection' => new JsExpression('function (user) { return user.text; }'),
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <?= Html::submitButton(Module::t('module', 'Confirm'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
