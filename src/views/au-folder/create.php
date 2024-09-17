<?php

use hesabro\automation\Module;


/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuFolder */

$this->title = Module::t('module', 'Create A Folder');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'A Folders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="afolder-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
