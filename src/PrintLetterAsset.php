<?php

namespace hesabro\automation;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PrintLetterAsset extends AssetBundle
{
    public $sourcePath = '@hesabro/automation';

    public $css = [
        'assets/fonts/IRANSans/style.css',
        'assets/css/print.letter.css',
    ];
    public $js = [
    ];
    public $depends = [
    ];
}