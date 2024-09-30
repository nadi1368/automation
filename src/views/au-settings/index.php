<?php

use common\models\ClientSettingsValue;
use common\models\Settings;
use common\widgets\grid\GridView;
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
                    'value' => function (Settings $model) {

                        if ($model->field == "boolean") {
                            if ($model->client_value == 1) {
                                return '<span class="text-success"><i class="ti-check"></i></span>';
                            } else {
                                return '<span class="text-danger"><i class="ti-close"></i></span>';
                            }
                        } elseif ($model->field == "item") {
                            return wrapContent($model->client_value ? $model->client_value . ' - ' . Settings::itemAlias($model->alias, $model->client_value) : '');
                        } elseif ($model->field == "itemMultiple") {
                            $items = Settings::itemAlias($model->alias);
                            $values = strpos((string)$model->client_value, ',') ? explode(',', (string)$model->client_value) : [$model->client_value];
                            $return = '';
                            if ($values) {
                                foreach ($values as $value) {
                                    if ($value) {
                                        $return .= '<span class="badge badge-info">' . $items[$value] . '</span> ';
                                    }
                                }
                            }

                            return $return;
                        } elseif ($model->field == 'image') {
                            return Html::img($model->getFileUrl('photo'), ['width' => '180px']);
                        } elseif ($model->field == "select2" && $model->client_value) {
                            return wrapContent($model->client_value . ' - ' . Settings::itemAlias('AliasValue', $model->alias, $model->client_value));
                        } else {
                            if ($model->field == 'number' && is_numeric($model->client_value)) {
                                $model->client_value = number_format((float)$model->client_value);
                            }
                            return wrapContent(
                                Html::a('<i class="fa fa-copy"></i>', 'javascript:void(0)', [
                                    'title' => 'کپی',
                                    'class' => 'js-copy-to-clipboard text-info',
                                    'data-content' => $model->client_value
                                ]) . ' ' . strip_tags((string)$model->client_value)
                            );
                        }
                    },
                    'format' => 'raw'
                ],
                //                [
                //                    'attribute' => 'alias',
                //                    'value' => function ($model) {
                //                        return $model->alias ? Settings::itemAlias('Alias', $model->alias) : '';
                //                    },
                //                    'filter' => Settings::itemAlias('Alias')
                //                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:auto;text-align:center;'],
                    'template' => '{group}',
                    'buttons' => [
                        'group' => function ($url, $model, $key) {
                            $items = [];
                            $items[] = [
                                'label' => Yii::t('app', 'Update'),
                                'url' => 'javascript:void(0)',
                                'linkOptions' => [
                                    'id' => 'update-setting-' . $key,
                                    'data-size' => 'modal-lg',
                                    'data-title' => Yii::t('app', 'Update'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-pjax',
                                    'data-url' => Url::to(['change-value', 'id' => $key]),
                                    'data-reload-pjax-container-on-show' => 0,
                                    'data-reload-pjax-container' => 'p-jax-setting',
                                    'data-handleFormSubmit' => 1,
                                ]
                            ];
                            $items[] = [
                                'label' => Yii::t('app', 'Log'),
                                'url' => ['/mongo/log/view-ajax', 'modelClass' => ClientSettingsValue::class, 'modelId' => $model->id],
                                'linkOptions' => [
                                    'data-size' => 'modal-xl',
                                    'class' => 'showModalButton',
                                    'title' => Yii::t('app', 'Log')
                                ],
                            ];
                            return ButtonDropdown::widget([
                                'buttonOptions' => ['class' => 'btn btn-info btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Yii::t('app', 'Actions')],
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