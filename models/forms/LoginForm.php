<?php

namespace app\models\forms;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\base\ErrorException;
use yii\web\Response;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;

    private $user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required', 'on' => User::SCENARIO_CHECK],
            [['username', 'password'], 'string', 'min' => 5],
            [['username', 'password'], 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute
     * @param array|null $params
     */

    public function validatePassword(string $attribute, ?array $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (($user === false) || !Yii::$app->security->validatePassword($this->password, $user->password)) {
                $this->addError($attribute, $user /*'Incorrectly entered  login or password'*/);
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     */
    public function login(): ?User
    {
        $this->setScenario(User::SCENARIO_CHECK);

        if ($this->validate()) {
            if($this->getUser()){

                $this->getUser()->access_token = (new User)->generateAccessToken();

                if (!$this->getUser()->update()) {
                    $this->addError('', 'Error not defined');

                    return null;
                }
                $this->getUser()->touch('edited_at');

                if (Yii::$app->user->login($this->getUser())){
                    return $this->getUser();
                }
            }
        }
        return null;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->user === false) {
            $this->user = User::findByUsername($this->username);
        }

        return $this->user;
    }
}
