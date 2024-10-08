<?php

use hesabro\automation\Module;


/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */

$this->title = Module::t('module', 'Create Letter Record');
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Letters Record'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="au-letter-create card">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
</div>
