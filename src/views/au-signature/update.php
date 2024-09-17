<?php

use hesabro\automation\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuSignature */

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Signature'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="au-signature-update card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
