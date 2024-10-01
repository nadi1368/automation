<?php

use hesabro\automation\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuPrintLayout */

$this->title = Module::t('module', 'Create');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Print Layouts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-print-layout-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
