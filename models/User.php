<?php

namespace app\models;

use app\models\MassiveData;
use yii\db\ActiveRecord;
use \yii\web\IdentityInterface;
use \yii\base\BaseObject;
use yii;

class User extends BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $ipUsr;
    public $loginDate;
    public $push;
    public $phone;

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @param string
     * @return string|bool
     */
    public function validateIpUsr($ipUsr)
    {
        return $this->ipUsr === $ipUsr;
    }

    /**
     * {@inheritdoc}
     * @param string
     * @return string|bool
     */
    public function validateAuthKey($authKey)
    {
        if (empty($this->authKey)){
            return $this->authKey === $authKey;
        } else {
            $user = new MassiveData();
            if ($this->authKey === $user->authKey){
                return $this->authKey;
            } else {
                return 'ключ авторизации разный' ;
            }
        }
    }

//Используется в LoginForm. Идёт проверка на валидность пароля.
    public function validatePassword($password)
    {
        if (!empty($this->password)){
            return Yii::$app->security->validatePassword($password, $this->password);
        } else {
            return $this->password === $password;
        }
    }

    /**
     * Identity user by id
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $user = MassiveData::findById($id);
        return isset($user) ? new static($user) : null;
    }

    /**
     * Generates access token
     * @return string
     */
    public function generateAccessToken()
    {
        return $this->accessToken = Yii::$app->security->generateRandomString(128);
    }

    /**
     * Identity user by Access token`s (SETTING IS NOT USED)
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $records = MassiveData::resultTableUsers();
        foreach ($records as $record) {
            if ($record['accessToken'] === $token) {
                return new static($record);
            }
        }
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
//Идентификация пользюка по username (используется при входе)
    public static function findByUsername($username)
    {
        $records = MassiveData::resultTableUsers();
        foreach ($records as $record) {
            if (strcasecmp($record['username'], $username) === 0) {
                return new static($record);
            }
        }
        return null;
    }

    public static function findByUsernameTest($username){
        $records = MassiveData::resultTableUsers();
        foreach ($records as $record) {
            if (strcasecmp($record['username'], $username) === 0) {
                return new static($record);
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
//Берёт поле loginDate и преобразует в норм формат
    public static function getLoginDate()
    {
        $id = Yii::$app->user->id;

        $recLogDate = MassiveData::getLoginDate($id);

        return $recLogDate;
    }

    /**
     * Populate model User from DB
     * @return bool
     */
//Если поля в БД пустые, они заполняются
    public function populateFieldsInDB(){
        if ($this->id === Yii::$app->user->id){
            return true;
        } else {
            $token = $this->generateAccessToken();

            $user = MassiveData::populateDB(Yii::$app->user->id, $token, Yii::$app->request->userIP);

            $this->id = Yii::$app->user->id;
            $this->username = $user->username;
            $this->password = $user->password;
            $this->authKey = $user->authKey;
            $this->ipUsr = $user->ipUsr;
            $this->loginDate = $user->loginDate;
            $this->push = $user->push;
            $this->phone = $user->phone;

            return true;
        }
    }



}
