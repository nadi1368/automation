<?php

use hesabro\automation\models\AuPrintLayout;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model hesabro\automation\models\AuLetter */
/* @var $printLayout hesabro\automation\models\AuPrintLayout */

$this->title = $model->title;
?>
    <div class="page-header">
        <div class="row">
            <div class="col-3 text-right" style="padding-right: 40px;">
                <?= $printLayout->getLogoImgForPrint() ?>
            </div>
            <div class="col-6">
                <?= ($printLayout->headerText) ?>
            </div>
            <?php if($printLayout->showTitleHeader): ?>
                <div class="col-3 text-left" style="padding-left: 40px;">
                    <p><b class="float-right">شماره :</b> <?= Html::encode($model->printNumber); ?></p>
                    <p><b class="float-right">تاریخ :</b> <?= Html::encode($model->date) ?></p>
                    <p><b class="float-right">پیوست :</b> <?= ($countAttach = $model->countAttach()) > 0 ? $countAttach : '' ?></p>
                </div>
            <?php else: ?>
                <div class="col-3 text-left" style="padding-left: 40px;">
                    <p><?= Html::encode($model->printNumber); ?></p>
                    <p><?= Html::encode($model->date) ?></p>
                    <p><?= ($countAttach = $model->countAttach()) > 0 ? $countAttach : '' ?></p>
                </div>
            <?php endif; ?>
        </div>
        <br>
    </div>

    <table>
        <thead>
        <tr>
            <td>
                <!--place holder for the fixed-position header-->
                <div class="page-header-space"></div>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <!--*** CONTENT GOES HERE ***-->
                <div class="page">
                    <div>
                        <?= nl2br((string)$model->body) ?>
                    </div>
                    <div class="<?= AuPrintLayout::itemAlias('TextAlign',$printLayout->signaturePosition) ?>">
                        <?= $this->render('_signatures', [
                            'model' => $model,
                        ]) ?>
                    </div>
                    <?php if ($cCRecipientsList = $model->showCCRecipientsList('print')): ?>
                        <div style="font-family:<?= $printLayout->fontCCRecipients ?>;">
                            <p>رونوشت:</p>
                            <?= $cCRecipientsList ?>
                        </div>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td>
                <!--place holder for the fixed-position footer-->
                <div class="page-footer-space"></div>
            </td>
        </tr>
        </tfoot>
    </table>


    <div class="page-footer"><?= $printLayout->footerText ?></div>
<?php
$sizePrint=AuPrintLayout::itemAlias('Size',$printLayout->size);
$logoHeight=($printLayout->headerHeight*0.9);
$css = <<< CSS

.page-header, .page-header-space {
    height: {$printLayout->headerHeight}cm;
    margin-bottom: 10px;
}

.page-footer, .page-footer-space {
    height:{$printLayout->footerHeight}cm;
    margin-top: 10px;
}

.page-logo {
    height:{$logoHeight}cm;
    margin-top: 3px;
}

.page-footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    padding-top: 10px;
    border-top: 1px solid black; /* for demo */
}

.page-header {
    position: fixed;
    top: 0;
    width: 100%;
    border-bottom: 1px solid black; /* for demo */
}
.page {
}


@page {
    size: {$sizePrint};
    margin-bottom: {$printLayout->marginBottom}cm;
    margin-top: {$printLayout->marginTop}cm;
    margin-left: {$printLayout->marginLeft}cm;
    margin-right: {$printLayout->marginRight}cm;
}

@media print {
    thead {display: table-header-group;}
    tfoot {display: table-footer-group;}
    button {display: none;}
}
CSS;
$this->registerCss($css);

?>