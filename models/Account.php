<?php


namespace app\models;

use yii\db\ActiveRecord;
use yii;
use yii\base\Model;

class Account extends ActiveRecord
{

    public $id = [];
    public $username = '';

    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    public static function tableName()
    {
        return 'test_table_users';
    }

    public function rules()
    {
        return [['username'], 'findUsernameById', 'on' => 'check'];
    }
    /**
     * @return array
    */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['check'] = ['username'];

        return $scenarios;
    }

    public function fields()
    {
        if ($this->getScenario() === 'check') {
            $fields[] = 'id';
            $fields[] = 'username';
        } else {
            $fields = parent::fields();
        }

        return $fields;
    }

    public function populate()
    {
        return $this->load(Yii::$app->getRequest()->getBodyParams());
    }

    public function fastFindAccount($username){

    }

    public function check(){
        $this->username = 'Пользователь не найден';
        if ($this->id && $account = $this->findUsernameById($this->id)){
            $this->username = $account['username'];
        }
        return $this->username;
    }

    public function findUsernameById($id){
        return self::find()->where(['id'=>$id])->asArray()->one();
    }
}
