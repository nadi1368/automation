<?php

use common\widgets\grid\GridView;
use hesabro\automation\Module;
use kartik\select2\Select2;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SettingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'تنظیمات';
$this->params['breadcrumbs'][] = $this->title;

$settingsClass=Module::getInstance()->settings;
$styles = <<<CSS
    .row-content-wrapper {
        max-width: 300px;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
        -webkit-line-clamp: 3;
    }
CSS;

$this->registerCss($styles);

function wrapContent($content)
{
    return '<div class="row-content-wrapper">' . $content . '</div>';
} ?>
<div class="card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
            </h4>
        </div>
    </div>
    <div class="card-body">
        <?php Pjax::begin(['id' => 'p-jax-setting']); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                return ['title' => $model->description];
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'name',
                'title',
                [
                    'attribute' => 'value',
                    'value' => function ($model) {
                        return $model->getVal();
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:auto;text-align:center;'],
                    'template' => '{group}',
                    'buttons' => [
                        'group' => function ($url, $model, $key) {
                            $items = [];
                            $items[] = [
                                'label' => Module::t('module', 'Update'),
                                'url' => 'javascript:void(0)',
                                'linkOptions' => [
                                    'id' => 'update-setting-' . $key,
                                    'data-size' => 'modal-lg',
                                    'data-title' => Module::t('module', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['change-value', 'id' => $key]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'p-jax-setting',
                                    'data-handleFormSubmit' => 1,
                                ]
                            ];
                            $items[] = [
                                'label' => Module::t('module', 'Log'),
                                'url' => ['/mongo/log/view-ajax', 'modelClass' => Module::getInstance()->clientSettingsValue, 'modelId' => $model->id],
                                'linkOptions' => [
                                    'data-size' => 'modal-xl',
                                    'class' => 'showModalButton',
                                    'title' => Module::t('module', 'Log')
                                ],
                            ];
                            return ButtonDropdown::widget([
                                'buttonOptions' => ['class' => 'btn btn-info btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Module::t('module', 'Actions')],
                                'encodeLabel' => false,
                                'label' => '<i class="far fa-list mr-1"></i>',
                                'dropdown' => [
                                    'encodeLabels' => false, // if you're going to use html on the items' labels
                                    'items' => $items,
                                    'options' => [
                                        'class' => 'dropdown-menu-left', // left dropdown
                                    ],
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>