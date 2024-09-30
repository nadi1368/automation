<?php

use yii\helpers\Html;
use common\widgets\grid\GridView;
use backend\modules\master\models\Client;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\master\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'اعضا';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'title',
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return Client::itemAlias('StatusTitle', $model->status);
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'link',
                    'value' => function ($model) {
                        $value = $model->linkPanel;
                        return Html::a('<i class="fa fa-copy"></i>', 'javascript:void(0)', [
                                'title' => 'کپی',
                                'class' => 'js-copy-to-clipboard text-info',
                                'data-content' => $value
                            ]) . ' ' . Html::a($value, $value);
                    },
                    'format' => 'raw'
                ],
            ],
        ]); ?>
    </div>
</div>
