<?php

namespace farawayslv\gallery;

use yii\web\AssetBundle;

class GalleryAsset extends AssetBundle
{
    public $sourcePath = '@vendor/farawayslv/yii2-gallery/assets';
    
    public $css = [
        'css/bootstrap.min.css',
        'css/lightbox.min.css',
        'css/gallery.css',
    ];
    public $js = [
        'js/jquery.min.js',
        'js/bootstrap.min.js',
        'js/lightbox.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
