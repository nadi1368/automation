<?php

use hesabro\automation\models\AuUser;
use hesabro\automation\Module;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\automation\models\AuUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Au Users');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="au-user-index card">
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
                        'data-reload-pjax-container' => 'p-jax-au-user',
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
                'options' => ['id' => 'p-jax-au-user']
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon' => '<span class="fal fa-chevron-down" style="font-size: 13px"></span>',
                    'collapseIcon' => '<span class="fal fa-chevron-up" style="font-size: 13px"></span>',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial('_index', [
                            'model' => $model,
                        ]);
                    },
                ],
                'id',
                'firstname',
                //'lastname',
                'phone',
                'mobile',
                //'email:email',
                //'address',
                //'description:ntext',
                //'user_id',
                //'additional_data',
                //'status',
                //'created_at',
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
                        'group' => function ($url, AuUser $model, $key) {
                            $items = [];

                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Update'),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'title' => Module::t('module', 'Update'),
                                    'data-title' => Module::t('module', 'Update'),
                                    'data-size' => 'modal-xl',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $key]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'p-jax-au-user',
                                ],
                            ];
                            $items[] = [
                                'label' => Html::tag('span', '', ['class' => 'fa fa-trash-alt']) . ' ' . Module::t('module', 'Delete'),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'data-confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                                    'title' => Module::t('module', 'Delete'),
                                    'aria-label' => Module::t('module', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-au-user',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $model->id]),
                                    'class' => "text-danger p-jax-btn",
                                    'data-title' => Module::t('module', 'Delete'),
                                    'data-method' => 'post'
                                ],
                            ];
                            $items[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) . ' ' . Module::t('module', 'Log'),
                                'url' => ['/change-log/default/view-ajax', 'modelId' => $model->id, 'modelClass' => hesabro\automation\models\AuUserBase::class],
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