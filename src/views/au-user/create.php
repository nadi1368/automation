<?php

use hesabro\automation\Module;


/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuUser */

$this->title = Module::t('module', 'Create Au User');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-user-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
