<?php

namespace masihfathi\form;

class FormAsset extends \yii\web\AssetBundle {

    public $sourcePath = '@masihfathi/form/assets/form';
    public $baseUrl = '@web';
    public $js = [
        'js/forms/helpers.js',
        'js/forms/form.js',
        'js/forms/field.js',
            //       'js/forms/form.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
