<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\automation\models\AuClientGroup */

$this->title = Yii::t('app', 'Create Au Client Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Au Client Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-client-group-create card">
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>
</div>
