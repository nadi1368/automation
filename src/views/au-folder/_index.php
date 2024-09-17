<?php

use hesabro\helpers\widgets\TableView;
use hesabro\automation\models\AuFolder;

/* @var $this yii\web\View */
/* @var $model AuFolder */

?>

<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'created_at',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdf::jdate("Y/m/d  H:i", $model->updated_at) . '">' . Yii::$app->jdf::jdate("Y/m/d  H:i", $model->created_at) . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created_by',
                    'value' => function ($model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update->fullName . '">' . $model->creator->fullName . '</span>';
                    },
                    'format' => 'raw'
                ],

            ]
        ]);
        ?>
    </div>
</div>
