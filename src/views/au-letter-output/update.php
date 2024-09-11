<?php

use hesabro\automation\models\AuLetter;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */

$this->title = Module::t('module', 'Update');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Letters Output'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Module::t('module', 'Update');
?>
<div class="au-letter-update card">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
