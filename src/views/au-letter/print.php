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
                <?php if ($model->canPrintWithSenderLayout()): ?>
                    <?= $printLayout->getLogoClientImgForPrint() ?>
                <?php else: ?>
                    <?= $printLayout->getLogoImgForPrint() ?>
                <?php endif; ?>
            </div>
            <div class="col-6">
                <?php if ($model->canPrintWithSenderLayout()): ?>
                    <?= $model->header_text; ?>
                <?php else: ?>
                    <?= ($printLayout->headerText) ?>
                <?php endif; ?>
            </div>
            <?= $printLayout->getTitleHtml($model) ?>
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
                        <?= (string)$model->body ?>
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


    <div class="page-footer">
        <?php if ($model->canPrintWithSenderLayout()): ?>
            <?= $model->footer_text; ?>
        <?php else: ?>
            <?= ($printLayout->footerText) ?>
        <?php endif; ?>
    </div>
<?php
$sizePrint = AuPrintLayout::itemAlias('Size', $printLayout->size);
$logoHeight = ($printLayout->headerHeight * 0.9);
$showBorderHeader=(int)$printLayout->showBorderHeader;
$showBorderFooter=(int)$printLayout->showBorderFooter;

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
    border-top: {$showBorderFooter}px solid black; /* for demo */
}

.page-header {
    position: fixed;
    top: 0;
    width: 100%;
    border-bottom: {$showBorderHeader}px solid black; /* for demo */
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