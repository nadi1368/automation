<?php

use hesabro\automation\models\AuLetter;
use hesabro\automation\models\AuUser;
use common\models\User;
use kartik\select2\Select2;
use skeeks\yii2\ckeditor\CKEditorPresets;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\widgets\CKEditorWidget;
use backend\modules\master\models\Client;
use hesabro\automation\models\AuFolder;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="au-letter-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-3">
                <?php Pjax::begin(['id' => 'p-jax-au-form']) ?>
                <?= $form->field($model, 'folder_id')->widget(Select2::class, [
                    'data' => AuFolder::itemAlias('ListOutput'),
                    'options' => ['placeholder' => Module::t('module', "Search")],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label($model->getAttributeLabel('folder_id') . Html::a('<span class="ti-plus"></span>',
                        'javascript:void(0)', [
                            'title' => Module::t('module', 'Create') . ' ' . $model->getAttributeLabel('folder_id'),
                            'id' => 'create-au-folder',
                            'class' => 'btn btn-outline-secondary pull-left',
                            'data-size' => 'modal-lg',
                            'data-title' => Module::t('module', 'Create') . ' ' . $model->getAttributeLabel('folder_id'),
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-pjax',
                            'data-url' => Url::to(['au-folder/create', 'type' => AuLetter::TYPE_OUTPUT]),
                            'data-reload-pjax-container-on-show' => 0,
                            'data-reload-pjax-container' => 'p-jax-au-form',
                        ]), ['class' => 'd-block']); ?>
                <?php Pjax::end() ?>
            </div>
            <div class="col-md-3 date-input">
                <?= $form->field($model, 'date')->widget(MaskedInput::class, ['mask' => '9999/99/99']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'sender_id')->widget(Select2::class, [
                    'data' => User::getUserWithRoles(['employee']),
                    'options' => [
                        'placeholder' => '',
                        'dir' => 'rtl',
                    ],
                ]); ?>
            </div>
            <?php if ($model->input_type == AuLetter::INPUT_OUTPUT_SYSTEM): ?>
                <div class="col-md-12">
                    <?= $form->field($model, 'recipients')->widget(Select2::class, [
                        'data' => Client::itemAlias('ParentBranches'),
                        'options' => [
                            'dir' => 'rtl',
                            'multiple' => true
                        ],
                    ]); ?>
                </div>
            <?php else: ?>
                <div class="col-md-4">
                    <?= $form->field($model, 'input_type')->dropdownList(AuLetter::itemAlias('InputTypeCreate'), ['prompt' => Module::t('module', 'Select')]) ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'recipients')->widget(Select2::class, [
                        'initValueText' => $model->recipients && is_array($model->recipients) ? ArrayHelper::map(AuUser::find()->andWhere(['IN', 'id', $model->recipients])->all(), "id", "fullName") : [],
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
            <?php endif; ?>
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
            <div class="col-md-12">
                <?= $form->field($model, 'body')->widget(CKEditorWidget::class, [
                    'options' => [
                        'class' => 'form-control description-fields',
                        'remo'
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
