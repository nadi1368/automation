<?php

namespace hesabro\automation\bundles;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PrintLetterAsset extends AssetBundle
{
    public $sourcePath = '@hesabro/automation/assets';

    public $css = [
        'fonts/IRANSans/style.css',
        'css/print.letter.css',
    ];
    public $js = [
    ];
    public $depends = [
    ];
}