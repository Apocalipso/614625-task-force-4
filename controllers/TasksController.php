<?php

namespace app\controllers;

use app\models\forms\TasksForm;
use Yii;
use app\models\Tasks;
use app\models\forms\TaskCreateForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\services\TaskCreateService;
use app\models\forms\CompleteTaskForm;
use app\models\forms\ResponseForm;
use app\models\Responses;

class TasksController extends AuthController
{
    public function actionIndex() {
        $taskForm = new TasksForm();
        $tasks = $taskForm->getTasks()->all();

        if (Yii::$app->request->getIsPost()){
            $taskForm->load(Yii::$app->request->post());
            if (!$taskForm->validate()) {
                $errors = $this->getErrors();
            } else {
                $tasks = $taskForm->getFilterTasks();
            }
        }
        return $this->render('task', ['tasks' => $tasks, 'model' => $taskForm]);
    }

    public function actionView($id)
    {
        $task = Tasks::findOne($id);

        $completeTaskForm = new CompleteTaskForm();
        $responseForm = new ResponseForm();

        if (!$task) {
            throw new NotFoundHttpException("По указанному id задача не найдена");
        }

        return $this->render('view', [
            'task' => $task,
            'CompleteTaskForm' => $completeTaskForm,
            'ResponseForm' => $responseForm
        ]);
    }

    public function actionCreate()
    {
        $user = Yii::$app->user->getIdentity();
        if ($user->is_customer === 0) {
            return $this->redirect('/tasks');
        }

        $taskCreateForm = new TaskCreateForm();

        if (Yii::$app->request->getIsPost()) {
            
            $taskCreateForm->load(Yii::$app->request->post());
            $taskCreateService = new taskCreateService();
            $taskCreateService->create($taskCreateForm);
        }

        return $this->render('create', ['taskCreateForm' => $taskCreateForm]);
    }

    //статус отклика меняет на принят 
    public function actionSubmit()
    {
        echo 'submit';
        exit;
    }

    //новый отклик на задание
    public function actionAccept($id)
    {
        $responseForm = new ResponseForm();
        $responseForm->load(Yii::$app->request->post());
        if ($responseForm->validate()) {
            $response = new Responses();
            $response->creation_time = date('Y-m-d H:i:s');
            $response->task_id = $id;
            $response->user_id = Yii::$app->user->identity->id;
            $response->text = $responseForm->text;
            $response->price = $responseForm->price;
            $response->save();
            return $this->redirect(['tasks/view', 'id' => $id]);
        }
    }

    //меняет статус отклика на отклонен по идентификатору
    public function actionCancelr($id,$responseId)
    {   
        $response = Responses::findOne($responseId);  
        $response->is_rejected = 1;      
        $response->update();

        return $this->redirect(['tasks/view', 'id' => $id]);
    }

    //меняет статус задания на отменено и отклоняет все отклики на него
    public function actionCancelt()
    {
        echo 'cancelt';
        exit;
    }

    //меняет статус задания на выполнено
    public function actionComplete(){
        echo 'complete';
        exit;
    }

    //меняет статус задания на провалено и отлоняет все отклики на него
    public function actionFail(){
        echo 'fail';
        exit;
    }
}
