<?php

use hesabro\automation\models\AuWorkFlowStep;
use hesabro\helpers\widgets\grid\GridView;

/**
 * @var \hesabro\automation\models\AuWorkFlow $model
 */

echo GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels' => $model->steps,
    ]),
    'layout' => "<div class='table-responsive mb-2'>{items}</div>",
    'columns' => [
        'step',
        'users' => [
            'attribute' => 'users',
            'value' => fn(AuWorkFlowStep $item) => $item->showUsersList(),
            'format' => 'raw'
        ],
        'operation_type' => [
            'attribute' => 'operation_type',
            'value' => fn(AuWorkFlowStep $item) => AuWorkFlowStep::itemAlias('OperationType', $item->operation_type),
            'format' => 'raw',
        ]
    ]
]);