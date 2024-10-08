<?php

use hesabro\automation\models\AuLetter;
use hesabro\automation\Module;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Html;
use hesabro\helpers\widgets\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel hesabro\automation\models\AuLetterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Module::t('module', 'Au Letters Record');
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
                <?= Html::a(Module::t('module', 'Create Letter Record'), ['create'], ['class' => 'btn btn-success']); ?>
            </div>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" aria-expanded="false">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget(['dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'type',
                    'value' => function (AuLetter $model) {
                        return AuLetter::itemAlias('Type', $model->type);
                    },
                    'format' => 'raw',
                ],
                'title',
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
                        return Html::tag('label',AuLetter::itemAlias('Status', $model->status), ['class' => 'badge badge-' . AuLetter::itemAlias('StatusClass', $model->status)]);
                    },
                    'format' => 'raw',
                ],
                [
                    'class' => 'common\widgets\grid\ActionColumn',
                    'template' =>'{view}{copy}',
                    'buttons' => [
                        'copy' => function ($url, AuLetter $model, $key) {
                            return Html::a('<span class="far fa-copy text-info"></span>', [AuLetter::itemAlias('TypeControllers', $model->type).'/create', 'copy_id' => $model->id], [
                                'title' => 'نسخه برداری از متن این نامه برای ایجاد نامه جدید',
                                'class' => 'target'
                            ]);
                        },
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>