<?php

use hesabro\automation\models\AuWorkFlow;
use hesabro\automation\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\bootstrap4\ButtonDropdown;

/* @var $this yii\web\View */
/* @var $type int */
/* @var $title string */
/* @var $items hesabro\automation\models\AuWorkFlow[] */

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Au Work Flows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Pjax::begin(['id' => 'p-jax-au-work-flow']); ?>
<div class="au-work-follow-view card">
    <div class="card-header d-flex justify-content-between">
        <div></div>
        <?= Html::a(Module::t('module', 'Create'),
            'javascript:void(0)', [
                'title' => Module::t('module', 'Create'),
                'data-title' => Module::t('module', 'Create'),
                'class' => 'btn btn-success',
                'data-size' => 'modal-lg',
                'data-toggle' => 'modal',
                'data-target' => '#modal-pjax',
                'data-url' => Url::to(['create', 'type' => $type]),
                'data-reload-pjax-container-on-show' => 0,
                'data-reload-pjax-container' => 'p-jax-au-work-flow',
            ]); ?>
    </div>
    <div class="card-body">
        <table class="table table-border">
            <thead>
            <tr>
                <th>ترتیب نمایش</th>
                <th>عنوان</th>
                <th>اشخاص</th>
                <th>نوع</th>
                <td></td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $index => $item): ?>
                <tr>
                    <td><?= $item->order_by ?></td>
                    <td><?= Html::encode($item->title); ?></td>
                    <td><?= $item->showUsersList() ?></td>
                    <td><?= AuWorkFlow::itemAlias('OperationType', $item->operation_type) ?></td>
                    <th><?php
                        $dropDownItems=[];
                        if ($item->canUpdate()) {
                            $dropDownItems[] = [
                                'label' => Html::tag('span', ' ', ['class' => 'fa fa-pen']) . ' ' . Module::t('module', 'Update'),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'title' => Module::t('module', 'Update'),
                                    'data-title' => Module::t('module', 'Update'),
                                    'data-size' => 'modal-lg',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['update', 'id' => $item->id]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'p-jax-au-work-flow',
                                ],
                            ];
                        }
                        if ($item->canDelete()) {
                            $dropDownItems[] = [
                                'label' => Html::tag('span', '', ['class' => 'fa fa-trash-alt']) . ' ' . Module::t('module', 'Delete'),
                                'url' => 'javascript:void(0)',
                                'encode' => false,
                                'linkOptions' => [
                                    'data-confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
                                    'title' => Module::t('module', 'Delete'),
                                    'aria-label' => Module::t('module', 'Delete'),
                                    'data-reload-pjax-container' => 'p-jax-au-work-flow',
                                    'data-pjax' => '0',
                                    'data-url' => Url::to(['delete', 'id' => $item->id]),
                                    'class' => "text-danger p-jax-btn",
                                    'data-title' => Module::t('module', 'Delete'),
                                    'data-method' => 'post'
                                ],
                            ];
                        }

                        $dropDownItems[] = [
                            'label' => Html::tag('span', ' ', ['class' => 'fa fa-history']) . ' ' . Module::t('module', 'Log'),
                            'url' => ['/mongo/log/view-ajax', 'modelId' => $item->id, 'modelClass' => get_class($item)],
                            'encode' => false,
                            'linkOptions' => [
                                'title' => Module::t('module', 'Log'),
                                'class' => 'showModalButton',
                                'data-size' => 'modal-xxl',
                            ],
                        ];

                        echo ButtonDropdown::widget([
                            'buttonOptions' => ['class' => 'btn btn-info btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Module::t('module', 'Actions')],
                            'encodeLabel' => false,
                            'label' => '<i class="far fa-list mr-1"></i>',
                            'options' => ['class' => 'float-right'],
                            'dropdown' => [
                                'items' => $dropDownItems,
                            ],
                        ]); ?></th>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php Pjax::end(); ?>
