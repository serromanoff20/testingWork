<?php

namespace app\models;

use app\models\forms\LoginForm;
use app\models\Goods;
use yii\behaviors\TimestampBehavior;
use yii\db\Connection;
use yii\db\ActiveRecord;
use yii\db\Expression;
use \yii\web\IdentityInterface;
use \yii\base\BaseObject;
use yii;


/**
 * Class User
 * @package app\models
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $access_token
 * @property string $email
 * @property integer $role
 * @property string user_ip
 * @property integer edited_at
 * @property TimestampBehavior last_login
 * @property integer created_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const SCENARIO_CREATE = 'create';

    public const SCENARIO_CHECK = 'check';

    public static function getDb(): Connection
    {
        return Yii::$app->getDb();
    }

    public static function tableName(): string
    {
        return 'testingWorkUsers';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'edited_at'
            ]
        ];
    }

    public static function listUsers()
    {
        return self::find()->asArray()->all();
    }

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
    public static function findIdentity($id): ?User
    {
        $user = self::findById($id);
        return (!!$user) ? new static($user) : null;
    }

    /**
     * Generates access token
     * @return string
     * @throws yii\base\Exception
     */
    public function generateAccessToken(): string
    {
        return $this->access_token = Yii::$app->security->generateRandomString(128);
    }

    /**
     * Identity user by Access token`s (SETTING IS NOT USED)
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): ?User
    {
        $records = self::listUsers();
        foreach ($records as $record) {
            if ($record['access_token'] === $token) {
                return new static($record);
            }
        }
        return null;
    }

    /**
     * Validate user by Access token`s
     * @param string $token
     * @return bool
     */
    public function validateUserByAccessToken(string $token): bool
    {
        $user = self::findIdentity(Yii::$app->user->id);
        if ($user->access_token === $token) {
            return true;
        }
        return false;
    }

    /**
     * Find user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username): ?ActiveRecord
    {
        return self::findOne(['username' => $username]);
    }

    /**
     * Find user by id
     *
     * @param $id
     * @return ActiveRecord|null
     */
    public static function findById($id): ?ActiveRecord
    {
        return self::findOne(['id' => $id]);
    }

    /**
     * @param $pass
     * @throws yii\base\Exception
     */
    public function generatedHashPass($pass): void
    {
        $this->password = Yii::$app->security->generatePasswordHash($pass);
    }

    /**
     * (SETTING IS NOT USED)
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * (SETTING and PROPERTY IS NOT USED)
     * @return mixed|string|null
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * (SETTING and PROPERTY IS NOT USED)
     * @param string $authKey
     * @return bool|void
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    public function logout($access_token): array
    {
        $user = $this->findById(Yii::$app->user->id);

        $accessTokenOfUser = $this->findIdentityByAccessToken($access_token);

        if ($user->access_token === $accessTokenOfUser->access_token) {
            Yii::$app->user->logout();

            return [$user];
        }
        $this->addError('', 'Need input "access-token" property in headers OR input token not find');

        return [];
    }
}
