<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Settings */

$this->title = $model->title;
?>
<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($model->title) ?></h5>
        <p><?= Html::encode($model->description) ?></p>
    </div>
    <?= $this->render('_form-value', [
        'model' => $model,
    ]) ?>
</div>
