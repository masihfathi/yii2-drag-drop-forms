<?php

use masihfathi\form\Form;

$this->title = Yii::t('app', 'Form') . ': ' . $form->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('builder', 'Forms'), 'url' => ['user']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('builder', 'List'), 'url' => ['list','form_id'=>$form->form_id]];
$this->params['breadcrumbs'][] = Yii::t('builder','Update ').' '.$this->title;
?>

<?=

Form::widget([
    'body' => $form->body,
    'typeRender' => 'php',
    'itemId' => $itemId
]);
?>
