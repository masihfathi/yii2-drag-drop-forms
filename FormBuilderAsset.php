<?php

namespace masihfathi\form;

class FormBuilderAsset extends \yii\web\AssetBundle {

    public $sourcePath = '@masihfathi/form/assets/form-builder';
    public $baseUrl = '@web';
    public $css = [
        'css/forms/quill.snow.css',
    ];
    public $js = [
        'js/forms/Sortable.min.js',
        //'js/forms/form.min.js',
        'js/forms/helpers.js',
        'js/forms/form.js',
        'js/forms/field.js',
        'js/forms/controller.js',
        'js/forms/form.module.response.js',
        'js/forms/examples.js',
        'js/forms/test.js',
        'js/forms/quill.js',
        'js/forms/clipboard.min.js',
        'js/forms/styles.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
