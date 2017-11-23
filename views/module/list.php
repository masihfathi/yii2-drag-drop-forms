<?php

use yii\widgets\Pjax;
use kartik\grid\GridView;

$this->title = $form->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('builder', 'Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
// Register action buttons js
$this->registerJs('$(document).ready(function() 
    {'
    .$searchModel->getUpdateButtonJavascript('#list-index')
    .$searchModel->getDeleteButtonJavascript('#list-index')
    .$searchModel->getPreviewButtonJavascript('#list-index').
    '});
');

?>

<div class="row">

    <!-- action buttons -->
    <div class="col-md-6 col-md-offset-6">

        <?php echo $searchModel->getExitButton(); ?>
        
        <?php echo $searchModel->getResetButton(); ?>

        <?php echo $searchModel->getPreviewButton(); ?>

        <?php echo $searchModel->getDeleteButton(); ?>

        <?php echo $searchModel->getUpdateButton(); ?>

        <?php echo $searchModel->getCreateButton(); ?>

    </div>

</div>

<div class="separator" style="margin: 1em;"></div>
<?php
$columns = array_merge(
         [
            [
                'class' => '\kartik\grid\CheckboxColumn'
            ],
             'id'
        ],
        $only_data_fields
        );
?>
<?php Pjax::begin(); ?>
<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'id'=>'list-index',
    'pjaxSettings' => [
        'neverTimeout' => true,
    ],
    'columns' => $columns,
    'responsive' => true,
    'hover' => true,
    'panel' => [
        'heading' => '<h3 class="panel-title"><i class="fa fa-file-text-o"></i></h3>',
        'type' => 'success',
    ],
]);
?>
<?php Pjax::end(); ?>
