<?php
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\files\models\Files */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('builder', 'Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
echo DetailView::widget([
    'model' => $model,
    'attributes' => $columns,
    'mode' => 'view',
    'panel' => [
        'type'=> 'success',
        'heading'=>Yii::t('builder','File'),
        'headingOptions'=>[
            'template'=>'{title}'
        ]
    ],
    'condensed' => false,
    'hover' => true,
    'bordered' => true,
    'striped' => true,
]);