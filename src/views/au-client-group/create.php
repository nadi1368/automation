<?php

use yii\helpers\Html;
use hesabro\automation\Module;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuClientGroup */

$this->title = Module::t('module', 'Create Au Client Group');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Client Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-client-group-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
