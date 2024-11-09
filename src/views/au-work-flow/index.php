<?php

use hesabro\automation\models\AuWorkFlow;
use hesabro\automation\models\AuWorkFlowBase;
use hesabro\automation\Module;
use hesabro\helpers\widgets\grid\GridView;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \hesabro\automation\models\AuWorkFlowSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider; */

$this->title = Module::t('module', 'Au Work Flow');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-work-follow-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?=
                Html::button(Module::t('module', 'Create'), [
                    'class' => 'btn btn-success',
                    'data-size' => 'modal-lg',
                    'data-title' => Module::t('module','Create') . ' ' . Module::t('module','Au Work Flow'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-pjax',
                    'data-url' => Url::to(['au-work-flow/create']),
                    'data-reload-pjax-container-on-show' => 0,
                    'data-reload-pjax-container' => 'p-jax-work-flow',
                ])
                ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => ['id' => 'p-jax-work-flow']
            ],
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
                    'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
                    'value' => fn ($model, $key, $index, $column) => GridView::ROW_COLLAPSED,
                    'detail' => function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial('_index', [
                            'model' => $model,
                        ]);
                    },
                ],
                'title',
                'letter_type' => [
                    'attribute' => 'letter_type',
                    'value' => fn(AuWorkFlow $model) => $model->letterTypeTitle
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function (AuWorkFlow $model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdf::jdate("Y/m/d  H:i", $model->updated_at) . '">' . Yii::$app->jdf::jdate("Y/m/d  H:i", $model->created_at) . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created_by',
                    'value' => function (AuWorkFlow $model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update?->fullName . '">' . $model->creator?->fullName . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:100px; text-align:left;'],
                    'template' => '{group}',
                    'buttons' => [
                        'group' => function ($url, AuWorkFlow $model, $key) {
                            $items = [];

                            if ($model->canUpdate()) {
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Update'),
                                    'url' => ['update', 'id' => $model->id],
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Update'),
                                        'data-title' => Module::t('module', 'Update'),
                                        'data-url' => Url::to(['update', 'id' => $model->id]),
                                        'data-pjax' => '0',
                                        'data-size' => 'modal-lg',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-reload-pjax-container-on-show' => 0,
                                        'data-reload-pjax-container' => 'p-jax-work-flow',
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
                                        'data-reload-pjax-container' => 'p-jax-work-flow',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['delete', 'id' => $model->id]),
                                        'class' => "text-danger p-jax-btn",
                                        'data-title' => Module::t('module', 'Delete'),
                                        'data-method' => 'post'
                                    ],
                                ];
                            }

                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) . ' ' . Module::t('module', 'Log'),
                                'url' => ['/change-log/default/view-ajax', 'modelId' => $model->id, 'modelClass' => AuWorkFlowBase::class],
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
