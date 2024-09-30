<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\automation\models\AuClientGroup */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Au Client Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-client-group-view card">
	<div class="card-body">
	<?= DetailView::widget([
		'model' => $model,
		'attributes' => [
            'id',
            'title',
            'additional_data',
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
		<?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id, 'slave_id' => $model->slave_id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id, 'slave_id' => $model->slave_id], [
		'class' => 'btn btn-danger',
		'data' => [
		'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
		'method' => 'post',
		],
		]) ?>
	</div>
</div>
