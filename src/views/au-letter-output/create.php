<?php

use hesabro\automation\models\AuLetter;
use hesabro\automation\Module;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */

$this->title = $model->input_type==AuLetter::INPUT_OUTPUT_SYSTEM ? Module::t('module', 'Create Letter Output Between System') : Module::t('module', 'Create Letter Output Out of System');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Letters Output'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-letter-create card">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
