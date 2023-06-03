<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\forms\RegForm;
use yii\base\InvalidConfigException;
use app\models\forms\LoginForm;

class AuthController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['registration', 'login', 'logout'],
            'rules' => [
                [
                    'actions' => ['registration', 'login'],
                    'allow' => true,
                    'roles' => ['?', '@'] //all users
                ],
                [
                    'actions' => ['logout'],
                    'allow' => true,
                    'roles' => ['@'] //only log in users
                ]
            ]
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'registration' => ['post'],
                'login' => ['post'],
                'logout' => ['get']
            ]
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
            'languages' => [
                'ru-RU',
            ],
        ];

        return $behaviors;
    }

    public function actionRegistration(): array
    {
        try {
            if (Yii::$app->request->isPost) {
                $modelUser = new User();

                if (!Yii::$app->user->isGuest) {
                    $user = $modelUser->findIdentity(Yii::$app->user->id);

                    return array("data" => $user, "messages" => ['You must log out to register.'], "code" => 200);
                }
                $params = Yii::$app->getRequest()->getBodyParams();

                $username = (!!$params['username']) ? $params['username'] : null;
                $pass_1 = (!!$params['password1']) ? $params['password1'] : null;
                $pass_2 = (!!$params['password2']) ? $params['password2'] : null;
                $email = (!!$params['email']) ? $params['email'] : null;

                $model = new RegForm();
                $model->load([
                    'username' => $username,
                    'password1' => $pass_1,
                    'password2' => $pass_2,
                    'email' => $email,
                ], '');

                if ($user = $model->registration()) {
                    return array(
                        'data' => $user,
                        'message' => 'success',
                        'code' => 200
                    );
                }
                return array(
                    'data' => [],
                    'message' => $model->getErrors(),
                    'code' => 500
                );
            }

            return array("data" => [], "message" => 'This must be POST-request', "code" => 500);
        } catch (InvalidConfigException $e) {
            return array("data" => [], "message" => 'Undefined error', "code" => 500);
        }
    }

    /**
     * Login action.
     */
    public function actionLogin(): array
    {
        try {
            $params = Yii::$app->getRequest()->getBodyParams();

            $username = (!!$params['username']) ? $params['username'] : null;
            $password = (!!$params['password']) ? $params['password'] : null;

            $modelForm = new LoginForm();

            if (
                $modelForm->load([
                    'username' => $username,
                    'password' => $password,
                ], '')
            ) {

                return array(
                    'data' => $modelForm->login(),
                    'message' => 'success',
                    'code' => 200,
                );
            }

            return array(
                'data' => [],
                'code' => 500,
                'message' => $modelForm->getErrors(),
            );
        } catch (\Exception $exception) {
            return array(
                'data' => [],
                'code' => 500,
                'message' => 'Error: Username and Password - required params. This POST-request.',
            );
        }
    }

    /**
     * Logout action.
     */
    public function actionLogout(): array
    {
        if (Yii::$app->request->isGet) {
            $model = new User();

            foreach (getallheaders() as $name => $value) {
                if ($name === 'Access-Token') {

                    if ($userLogout = $model->logout($value)) {
                        return array("data" => $userLogout, "message" => 'User is logout success', "code" => 200);
                    }
                }
            }
            return array("data" => [], "message" => $model->getErrors(), "code" => 400);
        }

        return array("data" => [], "message" => [], "code" => 500);
    }
}