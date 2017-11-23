<?php
use yii\bootstrap\Alert;

echo Alert::widget([
    'options' => [
        'class' => 'alert-info',
    ],
    'body' => Yii::t('builder','Form successfully submitted'),
]);
