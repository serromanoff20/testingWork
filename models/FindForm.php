<?php
namespace app\models;

use yii\base\Model;
use app\models\Account;

class FindForm extends Model
{
    public $id = '';

    public $username = '';

    public function rules()
    {
        return [['id'], 'required'];
    }
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function check(){
        $this->username = 'Пользователь не найден';
        if ($account = $this->findUsernameById($this->id)){
            $this->username = $account['username'];
        }
        return $this->username;
    }

    public function findUsernameById($id){
        return Account::find()->where(['id'=>$id])->one();
    }
}
