<?php

use hesabro\automation\Module;
use hesabro\automation\models\AuClientGroup;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use common\widgets\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\automation\models\AuClientGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'گروه بندی اعضا';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-client-group-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?= Html::a(Module::t('module', 'Create'),
                    'javascript:void(0)', [
                        'title' => Module::t('module', 'Create'),
                        'data-title' => Module::t('module', 'Create'),
                        'class' => 'btn btn-success',
                        'data-size' => 'modal-lg',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-pjax',
                        'data-url' => Url::to(['create']),
                        'data-reload-pjax-container-on-show' => 0,
                        'data-reload-pjax-container' => 'p-jax-au-client-group',
                    ]); ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => ['id' => 'p-jax-au-client-group']
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'title',
                [
                    'attribute' => 'clients',
                    'value' => function (AuClientGroup $model) {
                        return $model->showClients;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'status',
                    'value' => function (AuClientGroup $model) {
                        return Yii::$app->helper::itemAlias('YesOrNoIcon', $model->status);
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:100px;text-align:left;'],
                    'template' => '{group}',
                    'buttons' => [
                        'group' => function ($url, AuClientGroup $model, $key) {
                            $items = [];

                            if($model->canUpdate()) {
                                $items[] = [
                                    'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Update'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'title' => Module::t('module', 'Update'),
                                        'data-title' => Module::t('module', 'Update'),
                                        'data-size' => 'modal-lg',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-pjax',
                                        'data-url' => Url::to(['update', 'id' => $model->id]),
                                        'data-reload-pjax-container-on-show' => 0,
                                        'data-reload-pjax-container' => 'p-jax-au-client-group',
                                    ],
                                ];
                            }
                            if($model->canActive())
                            {
                                $items[] = [
                                    'label' =>  Html::tag('span', '', ['class' => 'fa fa-check-circle']) .' '.Module::t('module', 'Active'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'data-confirm-text' => Module::t('module', 'Are you sure?'),
                                        'data-reload-pjax-container' => 'p-jax-au-client-group',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['set-active', 'id' => $model->id]),
                                        'class' => "text-success p-jax-btn",
                                        'data-method' => 'post'
                                    ],
                                ];
                            }
                            if($model->canInActive())
                            {
                                $items[] = [
                                    'label' =>  Html::tag('span', '', ['class' => 'fa fa-minus-circle']) .' '.Module::t('module', 'Inactive'),
                                    'url' => 'javascript:void(0)',
                                    'encode' => false,
                                    'linkOptions' => [
                                        'data-confirm-text' => Module::t('module', 'Are you sure?'),
                                        'data-reload-pjax-container' => 'p-jax-au-client-group',
                                        'data-pjax' => '0',
                                        'data-url' => Url::to(['set-in-active', 'id' => $model->id]),
                                        'class' => "text-danger p-jax-btn",
                                        'data-method' => 'post'
                                    ],
                                ];
                            }
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) .' '. Module::t('module', 'Log'),
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
