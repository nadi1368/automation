<?php

use hesabro\automation\models\AuLetter;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use common\widgets\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\automation\models\AuLetterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'My Letters');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-letter-index card">
    <div class="panel-group m-bot20" id="accordion">
        <div class="card-header d-flex justify-content-between">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion"
                   href="#collapseOne" aria-expanded="false">
                    <i class="far fa-search"></i> جستجو
                </a>
            </h4>
            <div>
                <?php

                $items[] = [
                    'label' => Module::t('module', 'Create Letter Internal'),
                    'url' => ['au-letter-internal/create'],
                    'encode' => false,
                    'linkOptions' => [
                    ],
                ];

                $items[] = [
                    'label' => Module::t('module', 'Create Letter Input'),
                    'url' => ['au-letter-input/create', 'type' => AuLetter::TYPE_INPUT],
                    'encode' => false,
                    'linkOptions' => [
                    ],
                ];
                $items[] = [
                    'label' => Module::t('module', 'Create Letter Output'),
                    'url' => ['au-letter-output/create'],
                    'encode' => false,
                    'linkOptions' => [
                    ],
                ];

                echo ButtonDropdown::widget([
                    'buttonOptions' => ['class' => 'btn btn-success btn-md dropdown-toggle', 'style' => 'padding: 3px 7px !important;', 'title' => Module::t('module', 'Actions')],
                    'encodeLabel' => false,
                    'label' => Module::t('module', 'Create'),
                    'options' => ['class' => 'float-right'],
                    'dropdown' => [
                        'items' => $items,
                    ],
                ]); ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget(['dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'rowOptions' => function (AuLetter $model, $index, $widget, $grid) {
                if (!$model->viewed) {
                    return ['class' => 'warning font-bold', 'data-id' => $model->id];
                }
            },
            'columns' => [['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'type',
                    'value' => function (AuLetter $model) {
                        return AuLetter::itemAlias('Type', $model->type);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'title',
                    'value' => function (AuLetter $model) {
                        return Html::tag('i', '', ['class' => AuLetter::itemAlias('ViewedIcon', (int)$model->viewed) . ' mr-1']).$model->title;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'folder_id',
                    'value' => function (AuLetter $model) {
                        return $model->folder?->title;
                    },
                    'format' => 'raw',
                ],
                'number',
                'date',
                [
                    'attribute' => 'status',
                    'value' => function (AuLetter $model) {
                        return AuLetter::itemAlias('Status', $model->status);
                    },
                    'format' => 'raw',
                ],
                [
                        'class' => 'common\widgets\grid\ActionColumn',
                        'template' =>'{view}',
                        'buttons' => [
                            'view' => function ($url,AuLetter  $model, $key) {
                                return Html::a('<span class="far fa-eye text-info"></span>', [AuLetter::itemAlias('TypeControllers', $model->type).'/view', 'id' => $key], [
                                    'title' => Yii::t('yii', 'View'),
                                    'class' => 'target'
                                ]);
                            },
                        ]
                ],
            ],
        ]); ?>
    </div>
</div>
