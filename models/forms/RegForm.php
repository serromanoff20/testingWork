<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\rbac\Role;

class RegForm extends Model
{
    public $username;
    public $email;
    public $password1;
    public $password2;

    public function rules(): array
    {
        return [
            [['username', 'password1', 'password2'], 'required', 'on' => [User::SCENARIO_CREATE]],
            [['username', 'password1', 'password2'], 'string', 'min' => 5],
            ['email', 'email'],
            [['password1', 'password2'], 'validatePassword'],
            ['username', 'unique', 'targetClass' => '\app\models\User'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->password1 !== $this->password2) {
                $this->addError($attribute, 'Passwords not equal');
            }
        }
    }

    public function registration(): ?User
    {
        $this->setScenario(User::SCENARIO_CREATE);

        if ($this->validate()) {
            $model = new User();

            $accessToken = $model->generateAccessToken();

            $model->username = $this->username;
            $model->generatedHashPass($this->password1);
            $model->access_token = $accessToken;
            $model->email = $this->email;
            $model->user_ip = Yii::$app->request->userIP;
            $model->role = ($this->isAdmin($model->email)) ? 2 : 1;

            if ($model->save()) {
                return $model;
            }
            $this->addError('', 'Error in create user');

            return null;
        }

        $this->addError('', 'Incorrectly entered  login or password');

        return null;
    }

    public function isAdmin($email): bool
    {
        return ($email === Yii::$app->params['adminEmail']);
    }


}