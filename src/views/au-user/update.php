<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuUser */

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="au-user-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
