<?php

use yii\helpers\ArrayHelper;
use masihfathi\form\bootstrap\ActiveForm;
use masihfathi\form\models\DynamicModel;
use masihfathi\form\FormBase;

if (isset($itemId)) {
    $onlyDataFields = FormBase::onlyCorrectDataFields($form_body);
    $columns = ArrayHelper::getColumn($onlyDataFields, 'name');
    // in the dynamic model form_id of table get via query params
    $dynamicModel = new DynamicModel();
    $dynamicModel->items = $columns;
    $itemModel = $dynamicModel->findOne($itemId);
}
$script = null;
$form = ActiveForm::begin();
if (count($form_body) != 0) {
    foreach ($form_body as $key => $row) {
        echo ('<div class="row">');
        foreach ($row as $key => $value) {
            if (isset($value['field']) && isset($value['name']) && isset($itemModel)) {
                if ($value['field'] == 'checkbox') {
                    $selectorValues = explode(",", $itemModel->{$value['name']});
                    $script .= "jQuery(\"[name='DynamicModel[{$value['name']}][]'\").val([" ;
                    foreach ($selectorValues as $selectorValue) {
                       $script .=  "'".$selectorValue."',";
                    }
                    $script .= "]);";
                } else {
                    $script .= "jQuery(\"[name='DynamicModel[{$value['name']}]'\").val(['" . $itemModel->{$value['name']} . "']);";
                }
            }
            echo $form->dynamicField($model, $value);
        }
        echo('</div>');
    }
}
ActiveForm::end();
$this->registerCss("
    .ql-align-center { text-align:center } 
    .ql-align-right { text-align:right }
");
if (!is_null($script)) {
    $this->registerJs($script);
}
