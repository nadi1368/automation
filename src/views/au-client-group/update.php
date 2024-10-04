<?php

use yii\helpers\Html;
use hesabro\automation\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuClientGroup */

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Client Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="au-client-group-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
