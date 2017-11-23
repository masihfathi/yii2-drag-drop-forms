<?php

namespace masihfathi\form\traits;

use Yii;
use yii\helpers\Url;
use kartik\helpers\Html;

use masihfathi\form\models\FormModel;
/**
 * Trait ViewsHelper
 */
trait ViewsHelpersTrait {

    /**
     * Return javascript for action preview button
     *
     * @param string $w
     * @return string
     * @throws \yii\base\InvalidParamExceptionte
    public function getPreviewButtonJavascript($w) {
        $params = Yii::$app->request->queryParams;
        if (isset($params['form_id'])) {
            $form_id = (int) $params['form_id'];
        }else {
            throw new \Exception('form id param not found');    
        }        
        return '$("a.btn-preview").click(function() {
            var selectedId = $("' . $w . '").yiiGridView("getSelectedRows");

            if(selectedId.length == 0) {
                alert("' . Yii::t('builder', 'Select at least one item') . '");
            } else if(selectedId.length>1){
                alert("' . Yii::t('builder', 'Select only 1 item') . '");
            } else {
                var url = "' . Url::to(['preview', 'form_id' => $form_id]) . '&&id="+selectedId[0];
                window.open(url,"_blank");
            }
        });';
    }
    /**
     * Return javascript for action delete button
     *
     * @param string $w
     * @return string
     * @throws \yii\base\InvalidParamException
     */
    public function getDeleteButtonJavascript($w) {
        $params = Yii::$app->request->queryParams;
        if (isset($params['form_id'])) {
            $form_id = (int) $params['form_id'];
        }else {
            throw new \Exception('form id param not found');    
        }
        return '$("a.btn-delete").click(function() {
            var selectedId = $("' . $w . '").yiiGridView("getSelectedRows");

            if(selectedId.length == 0) {
                alert("' . Yii::t('builder', 'Select at least one item') . '");
            } else {
                var choose = confirm("' . Yii::t('builder', 'Do you want delete selected items?') . '");

                if (choose == true) {
                    $.ajax({
                        type: \'POST\',
                        url : "' . Url::to(['deletemultiple', 'form_id' => $form_id]) . '&&id="+selectedId,
                        data : {ids: selectedId},
                        success : function() {
                            $.pjax.reload({container:"' . $w . '"});
                        }
                    });
                }
            }
        });';
    }
    /**
     * Return javascript for action update button
     *
     * @param string $w
     * @return string
     * @throws \yii\base\InvalidParamException
     */
    public function getUpdateButtonJavascript($w) {
        $params = Yii::$app->request->queryParams;
        if (isset($params['form_id'])) {
            $form_id = (int) $params['form_id'];
        }else {
            throw new \Exception('form id param not found');    
        }        
        return '$("a.btn-update").click(function() {
            var selectedId = $("' . $w . '").yiiGridView("getSelectedRows");
        
            if(selectedId.length == 0) {
                alert("' . Yii::t('builder', 'Select at least one item') . '");
            } else if(selectedId.length>1){
                alert("' . Yii::t('builder', 'Select only 1 item') . '");
            } else {
                var url = "' . Url::to(['update-item', 'form_id' => $form_id]) . '&&id="+selectedId[0];
                window.open(url,"_blank");
            }
        });';
    }

    /**
     * Return action exit button
     *
     * @return string
     */
    public function getExitButton() {
        return $this->getStandardButton('fa fa-sign-out text-blue', Yii::t('builder', 'Exit'), ['index']);
    }    
    /**
     * Return action reset button
     *
     * @return string
     */
    public function getResetButton() {
        $params = Yii::$app->request->queryParams;
        if (isset($params['form_id'])) {
            $form_id = (int) $params['form_id'];
        }else {
            throw new \Exception('form id param not found');    
        }
        return $this->getStandardButton('fa fa-repeat text-aqua', Yii::t('builder', 'Reset'), ['list', 'form_id' => $form_id], ['class' => 'btn btn-mini btn-reset', 'data-pjax' => 0]);
    }    
    /**
     * Return action preview button
     *
     * @return string
     */
    public function getPreviewButton() {
        return $this->getStandardButton('fa fa-eye text-blue', Yii::t('builder', 'Preview'), '#', ['class' => 'btn btn-mini btn-preview']);
    }
    /**
     * Return action delete button
     *
     * @return string
     */
    public function getDeleteButton() {
        return $this->getStandardButton('fa fa-trash text-red', Yii::t('builder', 'Delete'), '#', ['class' => 'btn btn-mini btn-delete']);
    }
    /**
     * Return action update button
     *
     * @return string
     */
    public function getUpdateButton() {
            return $this->getStandardButton('fa fa-pencil text-yellow', Yii::t('builder', 'Update'), '#', ['class' => 'btn btn-mini btn-update']);
    }    
    /**
     * Return action create button
     *
     * @return string
     */
    public function getCreateButton() {
        $params = Yii::$app->request->queryParams;
        if (isset($params['form_id'])) {
            $form_id = (int) $params['form_id'];
        } else {
            throw new \Exception('form id param not found');    
        }
        $form = FormModel::findModel($form_id);        
        return $this->getStandardButton('fa fa-plus-circle text-green', Yii::t('builder', 'Create'), ['view', 'url' => $form->url],['class' => 'btn btn-mini','target'=>'_blank']);
    }


    /**
     * Return action send button
     *
     * @return string
     */
    public function getSendButton() {
        return $this->getStandardButton('fa fa-paper-plane text-orange', Yii::t('builder', 'Send'), '#', ['class' => 'btn btn-mini btn-send']);
    }

    /**
     * Return standard button
     *
     * @param string $icon
     * @param string $title
     * @param string | array $url
     * @param array $class
     * @return string
     */
    public function getStandardButton($icon, $title, $url, $class = ['class' => 'btn btn-mini']) {
        return '<div class="pull-right text-center" style="margin-right: 25px;">' .
                Html::a('<i class="' . $icon . '"></i>', $url, $class) . '
                    <div>' . $title . '</div>
                </div>';
    }    

}
