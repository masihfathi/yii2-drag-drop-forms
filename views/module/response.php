<?php
use kartik\widgets\Alert;

echo Alert::widget([
    'type' => Alert::TYPE_SUCCESS,
    'title' => Yii::t('builder','Thank you!'),
    'icon' => 'glyphicon glyphicon-ok-sign',
    'body' => Yii::t('builder','Form successfully submitted'),
    'showSeparator' => true,
    //'delay' => 2000
]);
