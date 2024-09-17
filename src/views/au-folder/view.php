<?php

use hesabro\automation\Module;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuFolder */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'A Folders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="afolder-view card">
	<div class="card-body">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
            'id',
            'title',
            'start_number',
            'end_number',
            'description',
            'type',
            'status',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'deleted_at',
            'slave_id',
		],
	]) ?>
	</div>
	<div class="card-footer">
		<?= Html::a(Module::t('module', 'Update'), ['update', 'id' => $model->id, 'slave_id' => $model->slave_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Module::t('module', 'Delete'), ['delete', 'id' => $model->id, 'slave_id' => $model->slave_id], [
		'class' => 'btn btn-danger',
		'data' => [
		'confirm' => Module::t('module', 'Are you sure you want to delete this item?'),
		'method' => 'post',
		],
		]) ?>
	</div>
</div>
