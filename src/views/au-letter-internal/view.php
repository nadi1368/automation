<?php

use hesabro\automation\models\AuLetter;
use hesabro\helpers\widgets\TableView;
use hesabro\automation\Module;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('module', 'Au Letters Internal'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(['id'=>'p-jax-letter']) ?>
<div class="card">
    <div class="card-header bg-light">
        <?= $this->render('/au-letter/_btn-master', [
            'model' => $model,
        ]) ?>
        <?= $this->render('_btn', [
            'model' => $model,
        ]) ?>
    </div>
    <div class="card-body">
        <?= TableView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'type',
                    'value' => function (AuLetter $model) {
                        return AuLetter::itemAlias('Type', $model->type);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'workflow_id',
                    'value' => function (AuLetter $model) {
                        return $model->workFlow?->title;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'folder_id',
                    'value' => function (AuLetter $model) {
                        return $model->folder?->title;
                    },
                    'format' => 'raw',
                ],
                'number',
                'date',
                [
                    'attribute' => 'status',
                    'value' => function (AuLetter $model) {
                        return Html::tag('label',AuLetter::itemAlias('Status', $model->status), ['class' => 'badge badge-' . AuLetter::itemAlias('StatusClass', $model->status)]);
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function (AuLetter $model) {
                        return '<span title="بروز رسانی شده در ' . Yii::$app->jdf::jdate("Y/m/d  H:i", $model->updated_at) . '">' . Yii::$app->jdf::jdate("Y/m/d  H:i", $model->created_at) . '</span>';
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'created_by',
                    'value' => function (AuLetter $model) {
                        return '<span title="بروز رسانی شده توسط ' . $model->update?->fullName . '">' . $model->creator?->fullName . '</span>';
                    },
                    'format' => 'raw'
                ],
            ],
        ]) ?>
    </div>
    <div class="card-body border-bottom">
        <h4 class="mb-0"><?= Html::encode($model->title) ?></h4>
    </div>
    <div class="card-body border-bottom">
        <div class="d-flex no-block align-items-center mb-2">
            <div class="mr-2">گیرندگان:</div>
            <div class="">
                <?= $model->showRecipientsList() ?>
            </div>
        </div>
        <div class="d-flex no-block align-items-center mb-2">
            <div class="mr-2">رونوشت:</div>
            <div class="">
                <?= $model->showCCRecipientsList() ?>
            </div>
        </div>
        <div class="d-flex no-block align-items-center mb-2">
            <div class="mr-2">ارجاع:</div>
            <div class="">
                <?= $model->showReferenceList() ?>
            </div>
        </div>
        <div class="mb-5"></div>
        <?= nl2br((string)$model->body) ?>
        <div class="mb-5"></div>
        <?= $this->render('/au-letter/_signatures', [
            'model' => $model,
        ]) ?>
        <div class="mb-5"></div>
        <?= $this->render('/au-letter/_steps', [
            'model' => $model,
        ]) ?>
    </div>
</div>

<?= $this->render('/au-letter/_activity', [
    'model' => $model,
]) ?>
<?php Pjax::end() ?>
