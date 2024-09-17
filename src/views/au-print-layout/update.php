<?php

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuPrintLayout */

use hesabro\automation\Module;

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Print Layouts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="au-print-layout-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
