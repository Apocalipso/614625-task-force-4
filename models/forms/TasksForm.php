<?php 
namespace app\models\forms;

use Yii;
use app\models\Categories;
use app\models\Tasks;
use yii\base\Model;
use yii\db\ActiveQuery;

class TasksForm extends Model
{
    public $category = [];
    public $noExecutor = false;
    public $period;

    const ONE_HOUR = 1;
    const DAY = 24;
    const WEEK = 168;
    //получить просто таски
    public function getTasks(){
        $activeQuery = Task::find();
        $activeQuery->joinWith('city');
        $activeQuery->joinWith('category');
        $activeQuery->where(['status' => Task::STATUS_NEW]);
        return $activeQuery;
    }
    //получить отфильтрованные таски
    public function getFilterTasks(){
        $activeQuery = $this->getTasks();
        
    }
}


