<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuSignature */

$this->title = Module::t('module', 'Create Au Signature');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Signature'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-signature-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
