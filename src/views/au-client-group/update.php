<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\automation\models\AuClientGroup */

$this->title = Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Au Client Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id, 'slave_id' => $model->slave_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="au-client-group-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
