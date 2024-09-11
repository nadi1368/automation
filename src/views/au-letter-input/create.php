<?php

use hesabro\automation\models\AuLetter;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */

$this->title = Module::t('module', 'Create Letter Input');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Letters Input'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-letter-create card">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
