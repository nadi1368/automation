<?php

use hesabro\automation\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuFolder */

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'A Folders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="afolder-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
