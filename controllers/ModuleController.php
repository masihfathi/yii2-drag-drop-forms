<?php

namespace masihfathi\form\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

use masihfathi\form\FormBase;
use masihfathi\form\FormBuilder;
use masihfathi\form\models\FormModel;
use masihfathi\form\models\FormModelSearch;
use masihfathi\form\widgets\email\Send as SendEmail;
use masihfathi\form\models\DynamicModel;

class ModuleController extends Controller {

    protected $list_action = ['create', 'update', 'delete', 'user','deletemultiple','preview','update-item'];

    /**
     * Event is triggered after form submit.
     * Triggered with \masihfathi\form\events\FormEvent
     */
    const EVENT_AFTER_SUBMIT = 'afterSubmit';

    /**
     * This method is invoked before any actions
     * @return array
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['user', 'create', 'update', 'delete', 'clone','deletemultiple','preview','update-item'],
                'rules' => $this->module->rules
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $searchModel = new FormModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'buttonsEditOnIndex' => $this->module->buttonsEditOnIndex,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUser() {
        $searchModel = new FormModelSearch();
        $searchModel->author = (isset(Yii::$app->user->identity->id)) ? Yii::$app->user->identity->id : null;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('user', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * view created form in this view user can submit form
     * @param string $url
     * @return string
     */
    public function actionView(string $url) {
        $form = FormModel::findModelByUrl($url);
        if ($form->endForm()) {
            return $this->render('end');
        }
        if (($data = Yii::$app->request->post('DynamicModel')) !== null) {

            $form_id = $form->form_id;

            foreach ($data as $i => $v) {
                if (is_array($data[$i]))
                    $data[$i] = join(',', $data[$i]);
            }
            $db = Yii::$app->{$this->module->db};
            $query = $db->createCommand()->insert($this->module->formDataTable . $form_id, $data);
            if ($query->execute()) {
                $last_id_inserted = $db->getLastInsertID();
                $form->answer = $this->updateAnswer($form_id);
                $form->save();
                Yii::$app->session->setFlash('success', Yii::t('builder', 'Form completed'));

                if ($this->module->sendEmail && is_string($this->module->emailSender) && isset($data['email']) && isset($form['response'])) {
                    SendEmail::widget([
                        'from' => $this->module->emailSender,
                        'to' => $data['email'],
                        'subject' => 'subject',
                        'textBody' => $form['response'],
                    ]);
                }
                // trigger afterSubmit event
                $event = Yii::createObject([
                        'class' => 'masihfathi\form\events\FormEvent',
                        'form_id'=>$form_id,
                        'item_id' => $last_id_inserted,
                        'form_data' => $data,
                        'form_name'=> $this->module->formDataTable . $form_id
                    ]
                );
                $this->trigger(self::EVENT_AFTER_SUBMIT,$event);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('builder', 'An confirmation email was not sent'));
            }
            $this->layout = '@app/views/layouts/main';
            return $this->render('response');
        } else {
            $this->layout = '@app/views/layouts/main';
            return $this->render('view', ['form' => $form]);
        }
    }
    /**
     * update an existing item
     * @return string
     * @throws \Exception
     */
    public function actionUpdateItem() {
        $params = Yii::$app->request->queryParams;
        if (!isset($params['form_id'])||!isset($params['id'])) {
            throw new \Exception('params not found');
        }
        $form_id = (int) $params['form_id'];
        $form = FormModel::findModel($form_id);
        // record item id
        $id = (int) $params['id'];
        $this->checkIfItemExists($id, $form->form_id);
        if (($data = Yii::$app->request->post('DynamicModel')) !== null) {

            foreach ($data as $i => $v) {
                if (is_array($data[$i]))
                    $data[$i] = join(',', $data[$i]);
            }
            $query = (new Query)->createCommand()->update($this->module->formDataTable . $form->form_id, $data,['id' =>$id]);
            if ($query->execute()) {

                Yii::$app->session->setFlash('success', Yii::t('builder', 'Form completed'));

                if ($this->module->sendEmail && is_string($this->module->emailSender) && isset($data['email']) && isset($form['response'])) {
                    SendEmail::widget([
                        'from' => $this->module->emailSender,
                        'to' => $data['email'],
                        'subject' => 'subject',
                        'textBody' => $form['response'],
                    ]);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('builder', 'An confirmation email was not sent'));
            }
            return $this->refresh();
        } else {
            $this->layout = '@app/views/layouts/main';
            return $this->render('update-item', ['form' => $form,'itemId'=>$id]);
        }
    }


    /**
     * list all item in grid view with filter and ...
     * @param int $form_id
     * @return string
     */
    public function actionList(int $form_id) {
        $form = FormModel::findModel($form_id);
        $form_body = Json::decode($form->body);
        $onlyDataFields = FormBase::onlyCorrectDataFields($form_body);
        $columns = ArrayHelper::getColumn($onlyDataFields, 'name');
        $searchModel = new DynamicModel();
        $searchModel->items = $columns;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $form_id, 10);

        return $this->render('list', [
            'form' => $form,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'only_data_fields' => $columns
        ]);
    }

    /**
     * Deletes selected Items models
     *
     * @throws NotFoundHttpException
     * @return  void
     */
    public function actionDeletemultiple() {
        $params = Yii::$app->request->queryParams;
        if (isset($params['form_id'])) {
            $form_id = (int) $params['form_id'];
        }
        $form = FormModel::findModel($form_id);
        $form_body = Json::decode($form->body);
        $onlyDataFields = FormBase::onlyCorrectDataFields($form_body);
        $columns = ArrayHelper::getColumn($onlyDataFields, 'name');
        $dynamicModel = new DynamicModel();
        $dynamicModel->items = $columns;
        $ids = Yii::$app->request->post('ids');

        if (!$ids) {
            return;
        }

        foreach ($ids as $id) {
            $model = $dynamicModel->findOne($id);

            if (!$model->delete()) {
                Yii::$app->session->setFlash('error', Yii::t('builder', 'Error deleting item'));
            }
        }
        $form->answer = $this->updateAnswer($form_id);
        $form->save();
        // Set Success Message
        Yii::$app->session->setFlash('success', Yii::t('builder', 'Delete Success!'));
    }

    /**
     * preview for each selected item
     * @throws  NotFoundHttpException
     */
    public function actionPreview() {
        $params = Yii::$app->request->queryParams;

        if (isset($params['form_id']) && isset($params['id'])) {
            $form_id = (int) $params['form_id'];
            $id = (int) $params['id'];
        } else {
            return;
        }
        $form = FormModel::findModel($form_id);
        $form_body = Json::decode($form->body);
        $onlyDataFields = FormBase::onlyCorrectDataFields($form_body);
        $columns = ArrayHelper::getColumn($onlyDataFields, 'name');
        $dynamicModel = new DynamicModel();
        $dynamicModel->items = $columns;
        $model = $dynamicModel->findOne($id);
        if($model!==null) {
            return $this->render('_detailview', [
                'columns' => $columns,
                'model' => $model
            ]);
        }
        throw new NotFoundHttpException('Item Not Found');
    }

    /**
     * Create Form action
     * @throws yii\base\InvalidParamException
     * @return string
     */
    public function actionCreate() {
        $r = Yii::$app->request;
        if ($r->isAjax) {
            $form = new FormBuilder([
                'db' => $this->module->db,
                'formTable' => $this->module->formTable,
                'formDataTable' => $this->module->formDataTable,
                'formData' => $r->post()
            ]);
            $form->save();
            $form->createTable();
            return $form->response();
        } else {
            return $this->render('create', [
                    'testMode' => $this->module->testMode,
                    'easyMode' => $this->module->easyMode
                ]
            );
        }
    }

    /**
     * Create Form action
     * @param integer $id
     * @throws yii\base\InvalidParamException
     * @return string
     */
    public function actionUpdate(int $id) {
        $form = new FormBuilder([
            'db' => $this->module->db,
            'formTable' => $this->module->formTable,
            'formDataTable' => $this->module->formDataTable,
        ]);

        $form->findModel($id);
        $r = Yii::$app->request;

        if ($r->isAjax) {
            \Yii::$app->response->format = 'json';

            switch (true) {
                case $r->isGet:
                    return $form->model;
                case $r->post('body'):
                    $form->load($r->post());
                    $form->save();
                    break;
                case $r->post('add'):
                    $form->addColumn($r->post('add'));
                    break;
                case $r->post('delete'):
                    $form->dropColumn($r->post('delete'));
                    break;
                case $r->post('change'):
                    $form->renameColumn($r->post('change'));
                    break;
                default:
                    return ['success' => false];
            }

            return ['success' => $form->success];
        } else {
            return $this->render('update', ['id' => $id, 'easyMode' => $this->module->easyMode
            ]);
        }
    }

    /**
     * Create Form action
     * @param integer $id
     * @throws yii\base\InvalidParamException
     * @return \yii\web\Response
     */
    public function actionClone(int $id) {

        $form = FormModel::find()->select(['body', 'title', 'author', 'date_start', 'date_start', 'maximum', 'meta_title', 'url', 'response'])->where(['form_id' => $id])->one();
        $form->answer = 0;
        $this->uniqueUrl($form);

        $db = Yii::$app->{$this->module->db};
        $db->createCommand()->insert($this->module->formTable, $form)->execute();

        $last_id = $db->getLastInsertID();
        $schema = FormBuilder::tableSchema($form->body);

        $db->createCommand()->createTable($this->module->formDataTable . $last_id, $schema, 'CHARACTER SET utf8 COLLATE utf8_general_ci')->execute();

        $this->redirect(['user']);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete(int $id) {
        $form = FormModel::findModel($id);
        $form->delete();
        return $this->redirect(['user']);
    }

    /**
     * Unique URL
     * @param FormModel $form
     * @return void
     */
    public function uniqueUrl(FormModel $form) {
        do {
            $form->url = $form->url . '_2';
            $count = FormModel::find()->select(['url'])->where(['url' => $form->url])->count();
        } while ($count > 0);
    }
    /**
     *
     * @param int $id
     * @param int $form_id
     * @return boolean if item exist return true else
     * @throws NotFoundHttpException
     */
    public function checkIfItemExists(int $id,int $form_id){
        $result =  (new Query)
            ->from($this->module->formDataTable . $form_id)
            ->where(['id'=>$id])
            ->count();
        if($result>0){
            return TRUE;
        }
        throw new NotFoundHttpException('Item Not Found');
    }
    /**
     * update answer column in parent table
     * @param integer $form_id
     * @return integer $numItems
     */
    public function updateAnswer(int $form_id) {
        $sql = "SELECT COUNT(*) FROM " . $this->module->formDataTable . $form_id;
        $db = Yii::$app->{$this->module->db};
        $numItems = $db
            ->createCommand($sql)
            ->queryScalar();
        return $numItems;
    }
}
