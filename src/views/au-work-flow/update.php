<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuWorkFlow */

$this->title = 'Update';
$this->params['breadcrumbs'][] = ['label' => 'Au Work Flow', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="au-work-follow-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
