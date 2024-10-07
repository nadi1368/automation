<?php

use hesabro\automation\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use common\widgets\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider; */

$this->title = 'Au Work Follows';
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
                'options' => ['id' => 'p-jax-work-flow']
            ],
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label' => 'نوع نامه',
                    'value' => function ($model) {
                        return $model;
                    },
                    'format' => 'raw'
                ],
                [
                        'class' => 'common\widgets\grid\ActionColumn',
                        'template' =>'{view}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="far fa-eye text-info"></span>', ['view', 'type' => $key], [
                                    'title' => Module::t('module', 'View'),
                                    'class' => 'target'
                                ]);
                            },
                        ]
                ],
            ],
        ]); ?>
    </div>
</div>
