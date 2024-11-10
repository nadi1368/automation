<?php

namespace hesabro\automation\bundles;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class JqueryUIAsset extends AssetBundle
{
    public $sourcePath = '@hesabro/automation/assets';

    public $js = [
        'js/jquery-ui.min.js'
    ];

    public $css = [
        'css/jquery-ui.min.css'
    ];

    public $depends = [
        JqueryAsset::class
    ];
}