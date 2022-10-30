<?php 
namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\Users;
use app\models\Cities;

class RegistrationForm extends Model
{
    public $name;
    public $email;
    public $cityId;
    public $password;
    public $repeatPassword;
    public $isExecutor;

    public function rules()
    {
        return [
            [['name', 'email', 'password', 'repeatPassword'], 'required'],
            [['name', 'email'], 'string', 'max' => 256],
            [['email'], 'email'],
            [['password', 'repeatPassword'], 'string', 'max' => 64],
            [['repeatPassword'], 'compare', 'compareAttribute' => 'password'],
            [['name'], 'unique', 'targetAttribute' => ['name' => 'name'], 'targetClass' => Users::class],
            [['email'], 'unique', 'targetAttribute' => ['email' => 'email'], 'targetClass' => Users::class],
            [['isExecutor'], 'boolean'],
            [['cityId'], 'exist', 'targetAttribute' => ['cityId' => 'id'], 'targetClass' => Cities::className()]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Ваше имя',
            'email' => 'Email',
            'cityId' => 'Город',
            'password' => 'Пароль',
            'repeatPassword' => 'Повтор пароля',
            'isExecutor' => 'я собираюсь откликаться на заказы'
        ];
    }

    public function createUser()
    {
        $user = new Users;
        $user->creation_time = date('Y-m-d G:i:s');
        $user->name = $this->name;
        $user->email = $this->email;
        $user->city_id = $this->cityId;
        $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
        $user->is_customer = 1;
        if($this->isExecutor){
            $user->is_customer = 0;
        }
        return $user;
    }
}