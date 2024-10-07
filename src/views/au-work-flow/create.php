<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuWorkFlow */

$this->title = 'Create Au Work Follow';
$this->params['breadcrumbs'][] = ['label' => 'Au Work Flow', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-work-follow-create card">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
