<?php

use hesabro\automation\bundles\PrintLetterAsset;
use hesabro\helpers\CKEditorFontAsset;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $content string */

PrintLetterAsset::register($this);
CKEditorFontAsset::register($this);
?>
<?php $this->beginPage() ?>
    <html dir="rtl" lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body onload="setTimeout(function (){window.print();}, 200);" bgcolor="#ffffff" text="#000000">
    <?php $this->beginBody() ?>

    <?= $content ?>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>