<?php

use hesabro\automation\models\AuSignature;
use common\components\Helper;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use common\widgets\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\automation\models\AuSignatureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Au Signature');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-signature-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::a(Module::t('module', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => ['id' => 'p-jax-au-signature']
            ],
//            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'title',
                [
                    'attribute' => 'user_id',
                    'value' => function (AuSignature $model) {
                        return $model->user?->fullName;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'users_other',
                    'value' => function (AuSignature $model) {
                        return $model->showUsersList();
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'signature',
                    'value' => function (AuSignature $model) {
                        return $model->signatureImg;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'status',
                    'value' => function (AuSignature $model) {
                        return Helper::itemAlias('YesOrNoIcon', $model->status);
                    },
                    'format' => 'raw',
                ],
                //'created_by',
                //'updated_at',
                //'updated_by',
                //'deleted_at',
                //'slave_id',

                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:100px;text-align:left;'],
                    'template' => '{group}',
                    'buttons' => [
                        'group' => function ($url, AuSignature $model, $key) {
                            $items = [];

                            if ($model->canUpdate()) {
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Update'),
                                    'url' => ['update', 'id' => $key],
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Update'),
                                        'data-title' => Module::t('module', 'Update'),
                                        'data-pjax' => 'false',
                                    ],
                                ];
                            }
                            if ($model->canDelete()) {
                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-trash-alt']) . ' ' . Module::t('module', 'Delete'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'data-confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                                        'title' => Module::t('module', 'Delete'),
                                        'aria-label' => Module::t('module', 'Delete'),
                                        'data-reload-pjax-container' => 'p-jax-au-signature',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['delete', 'id' => $model->id]),
                                        'class' => "text-danger p-jax-btn",
                                        'data-title' => Module::t('module', 'Delete'),
                                        'data-method' => 'post'
                                    ],
                                ];
                            }
                            if ($model->canActive()) {
                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-check-circle']) . ' ' . Module::t('module', 'Active'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'data-confirm-text' => Module::t('module', 'Are you sure?'),
                                        'data-reload-pjax-container' => 'p-jax-au-signature',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['set-active', 'id' => $model->id]),
                                        'class' => "text-success p-jax-btn",
                                        'data-method' => 'post'
                                    ],
                                ];
                            }
                            if ($model->canInActive()) {
                                $items[] = [
                                    'label' => Html::tag('span', '', ['class' => 'fa fa-minus-circle']) . ' ' . Module::t('module', 'Inactive'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'data-confirm-text' => Module::t('module', 'Are you sure?'),
                                        'data-reload-pjax-container' => 'p-jax-au-signature',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['set-in-active', 'id' => $model->id]),
                                        'class' => "text-danger p-jax-btn",
                                        'data-method' => 'post'
                                    ],
                                ];
                            }
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) . ' ' . Module::t('module', 'Log'),
                                'url' => ['/mongo/log/view-ajax', 'modelId' => $model->id, 'modelClass' => get_class($model)],
                                'encode' => false,
                                'linkOptions' => [
                                    'title' => Module::t('module', 'Log'),
                                    'class' => 'showModalButton',
                                    'data-size' => 'modal-xxl',
                                ],
                            ];

                            return ButtonDropdown::widget([
                                'buttonOptions' => ['class' => 'btn btn-info btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Module::t('module', 'Actions')],
                                'encodeLabel' => false,
                                'label' => '<i class="far fa-list mr-1"></i>',
                                'options' => ['class' => 'float-right'],
                                'dropdown' => [
                                    'items' => $items,
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
