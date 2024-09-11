<?php

use hesabro\automation\models\AuUser;
use common\models\Account;
use common\widgets\TableView;

/* @var $this yii\web\View */
/* @var $model AuUser */

?>
<div class="card">
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'email:email',
                'address',
                'description:ntext',
                [
                    'attribute' => 'created_at',
                    'value' => function (AuUser $model) {
                        return '<span title="بروز رسانی شده در '.Yii::$app->jdate->date("Y/m/d  H:i", $model->updated_at).'">'.Yii::$app->jdate->date("Y/m/d  H:i", $model->created_at).'</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created_by',
                    'value' => function (AuUser $model) {
                        return '<span title="بروز رسانی شده توسط '.$model->update?->fullName.'">'.$model->creator?->fullName.'</span>';
                    },
                    'format' => 'raw'
                ],
            ]
        ]);
        ?>
    </div>
</div>
